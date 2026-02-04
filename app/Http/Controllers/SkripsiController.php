<?php

namespace App\Http\Controllers;

use App\Models\Bookmark;
use Exception;
use Illuminate\Http\Request;
use App\Services\SkripsiService;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SkripsiController extends Controller
{
    /** @var \GuzzleHttp\Client */
    protected $httpClient;
    protected $skripsiService;


    public function __construct(SkripsiService $skripsiService)
    {
        // Inisialisasi Guzzle client jika belum di-inject
        $this->httpClient = new Client([
            'timeout' => 60,
            'connect_timeout' => 10,
            // 'verify' => false, // jangan pakai di production
        ]);
        
        $this->skripsiService = $skripsiService;
    }
    
    public function index()
    {
        $popular = $this->skripsiService->getTopSearchStatistics();

        [$defaultYear, $fakultasList] = $this->skripsiService->getDropdown();
              

        return view('skripsi.index', [
        'results' => session('skripsi_results') ?? false,
        'popularKeywords' => $popular,
        'fakultasList' => $fakultasList,
        'yearList' => $defaultYear
        ]);
    }

    public function search(Request $request)
    {

        $queryParams = [];
        $resultsService = null;

        if ($request->filled('judul'))    $queryParams['judul'] = $request->input('judul');
        if ($request->filled('page'))     $queryParams['page'] = $request->input('page');
        if ($request->filled('fakultas')) $queryParams['fakultas'] = $request->input('fakultas');
        if ($request->filled('prodi'))    $queryParams['prodi'] = $request->input('prodi');
        if ($request->filled('tahun'))     $queryParams['tahun'] = $request->input('tahun');
        
        try {
            $popular = $this->skripsiService->getTopSearchStatistics();
            $resultsService = $this->skripsiService->searchSkripsiFromApi($queryParams);

            if (!$resultsService) {
                throw new Exception("Data tidak ditemukan.");
            }
            
            [$defaultYear, $fakultasList] = $this->skripsiService->getDropdown();
            
            // Setup parameter untuk kebutuhan ui query pencarian
            $fieldsToPush = [
                'judul'    => 'judul_ta',
                'fakultas' => 'fakultas',
                'prodi'    => 'prodi',
                'tahun'     => 'tahun',
            ];
            
            foreach ($fieldsToPush as $paramKey => $resultKey) {
                if (isset($queryParams[$paramKey])) {
                    $resultsService[$resultKey] = $queryParams[$paramKey];
                }
            }
            
            // Konversikan indeks array ke NIM sebagai kunci agar lebih mudah diakses.
            if (isset($resultsService['data']) && is_array($resultsService['data'])) {
                $dataByNim = [];
                foreach ($resultsService['data'] as $student) {
                    if (isset($student['nim'])) {
                        $dataByNim[$student['nim']] = $student;
                    }
                }
                $resultsService['data'] = $dataByNim;
            }
            
            session(['skripsi_results' => $resultsService]);
            
        } catch (\Exception $e) {
            session(['skripsi_results' => []]);
            return redirect()->route('skripsi.index')->with('error', $e->getMessage());
        }

        return view('skripsi.index',  [
            'results' => $resultsService, 
            'popularKeywords' => $popular,
            'fakultasList' => $fakultasList,
            'yearList' => $defaultYear
        ]);
    }

    public function show($nim)
    {
        $dataSession = session('skripsi_results') ?? [];
        [$detail, $fileInfo] = $this->skripsiService->getDocumentFromSession($nim, $dataSession);
        $member = Auth::guard('member')->user();

        $detail['member_id'] = $member->member_id;
        $detail['member_name'] = $member->member_name;
        $detail['member_type_id'] = $member->member_type_id;

        
        
        $bookmarkFlagSession = $this->skripsiService->getBookmarkByMember($member->member_id, $detail);
  
        $detail['bookmarked'] = $bookmarkFlagSession;

        // optional: log
        if (isset($this->skripsiService) && method_exists($this->skripsiService, 'logSearchStat')) {
            $this->skripsiService->logSearchStat($detail);
        }

        return view('skripsi.detail', ['data' => $detail, 'pdf' => $fileInfo, 'nim' => $nim]);
    }

    /**
     * Menjelaskan fungsi streamPdf:
     *
     * Fungsi ini bertugas untuk mengambil file PDF skripsi dari sumber eksternal (remote) dan mengirimkannya ke browser pengguna secara streaming.
     * Fungsi ini juga mendukung permintaan partial content (HTTP Range), sehingga PDF bisa dipreview atau dilanjutkan (resume) di browser.
     *
     * Penjelasan langkah demi langkah:
     * 1. Mengambil data hasil pencarian skripsi dari session berdasarkan $nim.
     * 2. Mengecek apakah ada file PDF yang valid. Jika tidak ada, kembalikan response 404.
     * 3. Menyiapkan opsi permintaan HTTP, termasuk header User-Agent dan Range (jika ada).
     * 4. Menggunakan Guzzle HTTP client untuk mengambil file PDF dari URL sumber secara streaming.
     * 5. Jika terjadi error saat mengambil file (misal: server remote error), log error dan kembalikan response error ke pengguna.
     * 6. Jika server remote mengembalikan status error (>=400), ambil cuplikan body error dan forward ke pengguna.
     * 7. Jika berhasil, ambil header penting dari response remote (Content-Type, Content-Length, dsb).
     * 8. Stream isi file PDF ke browser pengguna secara bertahap (chunked), agar efisien untuk file besar.
     * 9. Response akan memiliki header yang sesuai agar browser bisa menampilkan PDF inline.
     *
     * Dengan cara ini, file PDF yang tidak berada di server aplikasi tetap bisa diakses pengguna dengan lancar dan aman.
     */
    public function streamPdf(Request $request, $nim)
    {
        // 1. Ambil data session hasil pencarian skripsi
        $dataSession = session('skripsi_results') ?? [];
        [$detail, $fileInfo] = $this->skripsiService->getDocumentFromSession($nim, $dataSession);

        // 2. Cek ketersediaan file PDF
        if (!$fileInfo || empty($fileInfo['url_file'])) {
            return response('File PDF tidak tersedia.', 404);
        }

        $url = $fileInfo['url_file'];
        //result url https://opac.uinssc.ac.id/skbpp/public/storage/dokumen/2025/FS/Hukum%20Ekonomi%20Syariah%2FMuamalah/2108202090/2108202090_9_fulltext.pdf
        // 3. Decode URL dan sanitasi path
        $decodedUrl = $this->sanitizeAndDecodeUrl($url);
        
        if (!$decodedUrl) {
            Log::error('Invalid or unsafe PDF URL', [
                'original_url' => $url,
                'nim' => $nim,
            ]);
            return response('URL file tidak valid atau tidak aman.', 400);
        }
        
        $rangeHeader = $request->header('Range');

        // 3. Siapkan opsi permintaan HTTP ke server remote
        $options = [
            'headers' => [
                'User-Agent' => $request->header('User-Agent') ?? 'Laravel-Proxy/1.0',
            ],
            'stream' => true,
            'http_errors' => false, // Jangan throw exception untuk error 4xx/5xx
            'allow_redirects' => true,
        ];

        if ($rangeHeader) {
            $options['headers']['Range'] = $rangeHeader;
        }

        // 4. Ambil file PDF dari server remote
        try {
            $resp = $this->httpClient->request('GET', $decodedUrl, $options);
        } catch (\Throwable $e) {
            // 5. Jika gagal, log dan kembalikan error 502
            Log::error('Guzzle error fetching PDF', [
                'url' => $decodedUrl,
                'nim' => $nim,
                'exception' => $e->getMessage(),
            ]);
            return response('Gagal menghubungi sumber file (gateway error).', 502);
        }

        $status = $resp->getStatusCode();

        // 6. Jika server remote error, forward status & cuplikan body
        if ($status >= 400) {
            $bodyPreview = '[no body]';
            try {
                $bodyPreview = $resp->getBody()->read(4096);
            } catch (\Throwable $e) {
                $bodyPreview = '[failed to read remote error body]';
            }

            Log::warning('Remote PDF returned error', [
                'url' => $decodedUrl,
                'status' => $status,
                'body_preview' => $bodyPreview,
            ]);

            return response($bodyPreview ?: "Remote returned status $status", $status)
                ->header('Content-Type', $resp->getHeaderLine('Content-Type') ?: 'text/plain');
        }

        // 7. Ambil header penting untuk response
        $contentType = $resp->getHeaderLine('Content-Type') ?: 'application/pdf';
        $filename = basename(parse_url($decodedUrl, PHP_URL_PATH) ?: 'file.pdf');

        $outHeaders = [
            'Content-Type' => $contentType,
            'Content-Disposition' => "inline; filename=\"{$filename}\"",
            'X-Content-Type-Options' => 'nosniff',
        ];

        foreach (['Content-Length', 'Content-Range', 'Accept-Ranges', 'Last-Modified', 'ETag'] as $h) {
            if ($resp->hasHeader($h)) {
                $outHeaders[$h] = $resp->getHeaderLine($h);
            }
        }

        // 8. Stream isi file PDF ke browser
        $body = $resp->getBody();
        $streamed = new StreamedResponse(function () use ($body) {
            while (!$body->eof()) {
                echo $body->read(1024 * 8);
                if (function_exists('ob_flush')) { @ob_flush(); }
                flush();
            }
        }, $status, $outHeaders);

        // 9. Kembalikan response streaming ke pengguna
        return $streamed;
    }

    /**
     * Sanitize and decode URL with security checks
     */
    protected function sanitizeAndDecodeUrl(string $url): ?string
    {
        // Parse URL untuk mendapatkan path
        $parsedUrl = parse_url($url);
        
        if (!$parsedUrl) {
            return null;
        }

        // Jika URL lengkap (dengan scheme), kembalikan setelah decode
        if (isset($parsedUrl['scheme'])) {
            // Decode hanya path portion untuk remote URLs
            if (isset($parsedUrl['path'])) {
                $decodedPath = urldecode($parsedUrl['path']);
                
                // Reconstruct URL dengan path yang sudah di-decode
                $parsedUrl['path'] = $decodedPath;
                return $this->unparseUrl($parsedUrl);
            }
            return $url;
        }

        // Untuk path lokal, decode dan sanitasi
        $decodedPath = urldecode($url);
        
        // Hapus null bytes (security)
        $decodedPath = str_replace("\0", '', $decodedPath);
        
        // Normalize path separators
        $decodedPath = str_replace('\\', '/', $decodedPath);
        
        // Check untuk directory traversal attempts
        if (
            str_contains($decodedPath, '../') || 
            str_contains($decodedPath, '..\\') ||
            str_contains($decodedPath, '...') ||
            preg_match('#\.\.[\\/]#', $decodedPath)
        ) {
            Log::warning('Directory traversal attempt detected', [
                'path' => $decodedPath,
                'original' => $url
            ]);
            return null;
        }

        return $decodedPath;
    }

    /**
     * Reconstruct URL from parsed components
     */
    protected function unparseUrl(array $parsed): string
    {
        $scheme   = isset($parsed['scheme']) ? $parsed['scheme'] . '://' : '';
        $host     = $parsed['host'] ?? '';
        $port     = isset($parsed['port']) ? ':' . $parsed['port'] : '';
        $user     = $parsed['user'] ?? '';
        $pass     = isset($parsed['pass']) ? ':' . $parsed['pass']  : '';
        $pass     = ($user || $pass) ? "$pass@" : '';
        $path     = $parsed['path'] ?? '';
        $query    = isset($parsed['query']) ? '?' . $parsed['query'] : '';
        $fragment = isset($parsed['fragment']) ? '#' . $parsed['fragment'] : '';
        
        return "$scheme$user$pass$host$port$path$query$fragment";
    }

    public function toggleBookmark(Request $request, $nim)
    {
        $dataSession = session('skripsi_results');
        if(empty($dataSession)){
            return redirect()->route('skripsi.index')->with('success', 'session data habis');
        }

        $user = Auth::guard('member')->user();

        $message = $this->skripsiService->toggleBookmark($nim, $dataSession, $user);

        return redirect()->back()->with('success', $message);
    }

}
