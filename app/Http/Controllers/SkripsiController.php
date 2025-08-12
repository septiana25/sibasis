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

        [$defaultFakultas, $defaultProdi, $defaultYear, $fakultasList] = $this->skripsiService->getDropdown();
              

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
        
        try {
            $popular = $this->skripsiService->getTopSearchStatistics();
            $resultsService = $this->skripsiService->searchSkripsiFromApi($queryParams);

            if (!$resultsService) {
                throw new Exception("Data tidak ditemukan.");
            }
            
            [$defaultFakultas, $defaultProdi, $defaultYear, $fakultasList] = $this->skripsiService->getDropdown();
            
            $fieldsToPush = [
                'judul'    => 'judul_ta',
                'fakultas' => 'fakultas',
                'prodi'    => 'prodi',
            ];
            
            foreach ($fieldsToPush as $paramKey => $resultKey) {
                if (isset($queryParams[$paramKey])) {
                    $resultsService[$resultKey] = $queryParams[$paramKey];
                }
            }
            
            session(['skripsi_results' => $resultsService]);
            
        } catch (\Exception $e) {
            return redirect()->route('skripsi.index')->with('error', $e->getMessage());
        }

        return view('skripsi.index',  [
            'results' => $resultsService, 
            'popularKeywords' => $popular,
            'fakultasList' => $fakultasList,
            'yearList' => $defaultYear
        ]);
    }

    public function show($index)
    {
        $dataSession = session('skripsi_results') ?? [];
        [$detail, $fileInfo] = $this->skripsiService->getDocumentFromSession($index, $dataSession);
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

        return view('skripsi.detail', ['data' => $detail, 'pdf' => $fileInfo, 'index' => $index]);
    }

    public function streamPdf(Request $request, $index)
    {
        $dataSession = session('skripsi_results') ?? [];
        [$detail, $fileInfo] = $this->skripsiService->getDocumentFromSession($index, $dataSession);

        if (!$fileInfo || empty($fileInfo['url_file'])) {
            return response('File PDF tidak tersedia.', 404);
        }

        $url = $fileInfo['url_file'];
        $rangeHeader = $request->header('Range');

        $options = [
            'headers' => [
                'User-Agent' => $request->header('User-Agent') ?? 'Laravel-Proxy/1.0',
            ],
            'stream' => true,
            'http_errors' => false, // jangan throw exceptions utk 4xx/5xx
            'allow_redirects' => true,
        ];

        if ($rangeHeader) {
            $options['headers']['Range'] = $rangeHeader;
        }

        try {
            $resp = $this->httpClient->request('GET', $url, $options);
        } catch (\Throwable $e) {
            Log::error('Guzzle error fetching PDF', [
                'url' => $url,
                'index' => $index,
                'exception' => $e->getMessage(),
            ]);
            return response('Gagal menghubungi sumber file (gateway error).', 502);
        }

        $status = $resp->getStatusCode();

        // Jika remote mengembalikan error, forward status & sedikit body preview
        if ($status >= 400) {
            $bodyPreview = '[no body]';
            try {
                $bodyPreview = $resp->getBody()->read(4096);
            } catch (\Throwable $e) {
                $bodyPreview = '[failed to read remote error body]';
            }

            Log::warning('Remote PDF returned error', [
                'url' => $url,
                'status' => $status,
                'body_preview' => $bodyPreview,
            ]);

            return response($bodyPreview ?: "Remote returned status $status", $status)
                ->header('Content-Type', $resp->getHeaderLine('Content-Type') ?: 'text/plain');
        }

        // Ambil header penting dan stream body
        $contentType = $resp->getHeaderLine('Content-Type') ?: 'application/pdf';
        $filename = basename(parse_url($url, PHP_URL_PATH) ?: 'file.pdf');

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

        $body = $resp->getBody();
        $streamed = new StreamedResponse(function () use ($body) {
            while (!$body->eof()) {
                echo $body->read(1024 * 8);
                if (function_exists('ob_flush')) { @ob_flush(); }
                flush();
            }
        }, $status, $outHeaders);

        return $streamed;
    }

    public function toggleBookmark(Request $request, $index)
    {
        $dataSession = session('skripsi_results');
        if(empty($dataSession)){
            return redirect()->route('skripsi.index')->with('success', 'session data habis');
        }

        $user = Auth::guard('member')->user();

        $message = $this->skripsiService->toggleBookmark($index, $dataSession, $user);

        return redirect()->back()->with('success', $message);
    }

}
