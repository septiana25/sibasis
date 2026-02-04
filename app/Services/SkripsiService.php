<?php

namespace App\Services;

use App\Models\Bookmark;
use App\Models\Fakultas;
use App\Models\SearchStatistic;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class SkripsiService
{

    public function __construct(protected SkripsiApiService $skripsiApiService) {}

    public function getDropdown(): array
    {
        /* data get database pakai ini
        $defaultFakultas = Fakultas::select('id', 'nama')->get()->toArray();
        $defaultProdi = Prodi::select('id', 'nama')->get()->toArray();
        */
        $defaultFakultas = [
            ['index' => 100, 'id' => 1, 'nama' => 'FITK'],
            ['index' => 100, 'id' => 2, 'nama' => 'FEBI'],
            ['index' => 200, 'id' => 3, 'nama' => 'FS'],
            ['index' => 100, 'id' => 4, 'nama' => 'FDKI'],
            ['index' => 300, 'id' => 5, 'nama' => 'FUA'],
            ['index' => 600, 'id' => 6, 'nama' =>  'S2'],
            ['index' => 700, 'id' => 7, 'nama' =>  'S3'],
        ];

        $fakultasList = Fakultas::with('prodi')->get();
        $defaultYear = ['year' => 2025, 'year' => 2024, 'year' => 2023, 'year' => 2022, 'year' => 2021];

        return [$defaultYear, $fakultasList];
    }

    public function searchSkripsiFromApi(array $queryParams)
    {
        return $this->skripsiApiService->getSkripsiFromApi($queryParams);
    }

    /**
     * Handle complete search process including data transformation and session storage
     */
    public function handleSearchProcess(array $queryParams): array
    {
        // Get API results
        $resultsService = $this->searchSkripsiFromApi($queryParams);

        if (!$resultsService) {
            throw new \Exception("Data tidak ditemukan.");
        }

        // Get dropdown data for view
        [$defaultYear, $fakultasList] = $this->getDropdown();

        // Add search parameters to results for UI display
        $resultsService = $this->addSearchParametersToResults($resultsService, $queryParams);

        // Transform array to use NIM as keys
        $resultsService = $this->transformDataToNimKeys($resultsService);

        // Store in session
        session(['skripsi_results' => $resultsService]);

        return [
            'results' => $resultsService,
            'fakultasList' => $fakultasList,
            'yearList' => $defaultYear
        ];
    }

    /**
     * Add search parameters to results for UI query display
     */
    private function addSearchParametersToResults(array $resultsService, array $queryParams): array
    {
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

        return $resultsService;
    }

    /**
     * Convert array indices to use NIM as keys for easier access
     */
    private function transformDataToNimKeys(array $resultsService): array
    {
        if (isset($resultsService['data']) && is_array($resultsService['data'])) {
            $dataByNim = [];
            foreach ($resultsService['data'] as $student) {
                if (isset($student['nim'])) {
                    $dataByNim[$student['nim']] = $student;
                }
            }
            $resultsService['data'] = $dataByNim;
        }

        return $resultsService;
    }

    /**
     * Prepare query parameters from request
     */
    public function prepareQueryParams(Request $request): array
    {
        $queryParams = [];

        $allowedFields = ['judul', 'page', 'fakultas', 'prodi', 'tahun'];

        foreach ($allowedFields as $field) {
            if ($request->filled($field)) {
                $queryParams[$field] = $request->input($field);
            }
        }

        return $queryParams;
    }

    public function getDocumentFromSession($nim, array $dataSession): array
    {

        if (!$dataSession || !isset($dataSession['data']) || !is_array($dataSession['data'])) {
            abort(404, 'Sesi data skripsi tidak ditemukan atau telah kedaluwarsa.');
        }

        // Validasi nim - check if nim exists as key in the data array
        if (!array_key_exists($nim, $dataSession['data'])) {
            abort(404, 'Data dengan NIM tersebut tidak ditemukan.');
        }

        $detail = $dataSession['data'][$nim];

        $dokumen = $detail['dokumen'] ?? [];
        if (!is_array($dokumen)) {
            $dokumen = [];
        }

        // Cari dokumen "Full Text" secara case-insensitive dan trimming spasi.
        // Menggunakan collect() untuk ringkasnya, dengan callback custom.
        $collection = collect($dokumen);
        $fileInfo = $collection->first(function ($item) {
            if (!is_array($item)) return false;
            if (!isset($item['nama_dokumen'])) return false;
            return mb_strtolower(trim($item['nama_dokumen'])) === 'full text';
        });

        // Fallback: jika tidak ketemu "Full Text", pilih dokumen pertama yang punya url_file
        if (!$fileInfo) {
            $fileInfo = $collection->first(function ($item) {
                return is_array($item) && !empty($item['url_file']);
            });
        }

        // Kembalikan detail dan fileInfo (bisa null kalau benar-benar tidak ada dokumen)
        return [$detail, $fileInfo];
    }

    public function logSearchStat(array $statistik)
    {
        $validator = Validator::make($statistik, [
            'member_id' => 'required',
            'member_name' => 'required',
            'member_type_id' => 'required',
            'nim' => 'required',
            'nama' => 'required',
            'judul_ta' => 'required',
            'program_studi' => 'required',
            'fakultas' => 'required',
        ]);


        if ($validator->fails()) {
            Log::error($validator->errors());
            return null;
        }

        // Check if a record already exists for the same member_id and nim today
        $existingRecord = SearchStatistic::where('member_id', $statistik['member_id'])
            ->where('nim', $statistik['nim'])
            ->whereDate('created_at', now()->toDateString())
            ->first();

        if ($existingRecord) {
            return null;
        }

        SearchStatistic::create([
            'member_id' => $statistik['member_id'],
            'member_name' => $statistik['member_name'],
            'member_type_id' => $statistik['member_type_id'],
            'nim' => $statistik['nim'],
            'nama' => $statistik['nama'],
            'judul_ta' => $statistik['judul_ta'],
            'program_studi' => $statistik['program_studi'],
            'fakultas' => $statistik['fakultas']
        ]);
    }

    public function getTopSearchStatistics($limit = 5)
    {
        return SearchStatistic::select('judul_ta', 'program_studi', DB::raw('count(*) as total'))
            ->groupBy('judul_ta', 'program_studi')
            ->orderByDesc('total')
            ->limit($limit)
            ->get();
    }

    public function toggleBookmark(string $nim, array $dataSession, object $user): string
    {
        // Pastikan struktur minimal ada
        if (!$dataSession || !isset($dataSession['data']) || !is_array($dataSession['data'])) {
            return 'data tidak ada';
        }

        // Pastikan nim ada
        if (!array_key_exists($nim, $dataSession['data'])) {
            return 'data dengan NIM tersebut tidak ditemukan';
        }

        [$detail, $fileInfo] = $this->getDocumentFromSession($nim, $dataSession);

        $bookmark = $this->getBookmark($user->member_id, $detail);

        if ($bookmark) {
            // jika sudah ada, hapus (unbookmark)
            $bookmark->delete();
            $this->setBookmarkFlagInSession($nim, false, $dataSession);
            return 'Bookmark dihapus.';
        }

        Bookmark::create([
            'member_id' => $user->member_id,
            'nim' => $detail['nim'],
            'member_name' => $user->member_name ?? null,
            'judul_ta' => $detail['judul_ta'],
            'meta' => [
                'saved_at_session_nim' => $nim,
            ],
        ]);

        $this->setBookmarkFlagInSession($nim, true, $dataSession);
        return 'Berhasil ditambahkan ke bookmark.';
    }

    private function setBookmarkFlagInSession(string $nim, bool $value, array $dataSession): void
    {
        // Pastikan struktur minimal ada
        if (!$dataSession || !isset($dataSession['data']) || !is_array($dataSession['data'])) {
            return;
        }

        // Pastikan nim ada
        if (!array_key_exists($nim, $dataSession['data'])) {
            return;
        }

        // Set flag 'bookmarked'
        $dataSession['data'][$nim]['bookmarked'] = $value;

        // Simpan kembali ke session
        session(['skripsi_results' => $dataSession]);
    }

    public function getBookmarkByMember(int $member_id, array $detail)
    {
        if (isset($detail['bookmarked']) && $detail['bookmarked']) return true;
        //if (isset($detail['bookmarked']) && !$detail['bookmarked'] ) return false;

        $bookmark = $this->getBookmark($member_id, $detail);

        if (!$bookmark) return;

        return true;
    }

    private function getBookmark(int $member_id, array $detail)
    {

        $nim = $detail['nim'] ?? null;
        $judul = $detail['judul_ta'] ?? null;

        $query = Bookmark::where('member_id', $member_id);
        if ($nim) $query->where('nim', $nim);
        else $query->where('judul_ta', $judul);

        return $query->first();
    }

    /**
     * Get PDF file info and validate URL for streaming
     */
    public function preparePdfStream(string $nim): array
    {
        //1. ambil data dari session
        $dataSession = session('skripsi_results') ?? [];
        [$detail, $fileInfo] = $this->getDocumentFromSession($nim, $dataSession);

        if (!$fileInfo || empty($fileInfo['url_file'])) {
            throw new \Exception('File PDF tidak tersedia.', 404);
        }

        //2. sanitize dan decode url
        $url = $fileInfo['url_file'];
        $decodedUrl = $this->sanitizeAndDecodeUrl($url);

        if (!$decodedUrl) {
            Log::error('Invalid or unsafe PDF URL', [
                'original_url' => $url,
                'nim' => $nim,
            ]);
            throw new \Exception('URL file tidak valid atau tidak aman.', 400);
        }

        return [
            'detail' => $detail,
            'fileInfo' => $fileInfo,
            'url' => $decodedUrl
        ];
    }

    /**
     * Prepare HTTP options for PDF request
     */
    public function preparePdfHttpOptions(Request $request): array
    {
        $rangeHeader = $request->header('Range');

        $options = [
            'headers' => [
                'User-Agent' => $request->header('User-Agent') ?? 'Laravel-Proxy/1.0',
            ],
            'stream' => true,
            'http_errors' => false,
            'allow_redirects' => true,
        ];

        if ($rangeHeader) {
            $options['headers']['Range'] = $rangeHeader;
        }

        return $options;
    }

    /**
     * Prepare response headers for PDF streaming
     */
    public function preparePdfHeaders(Response $response, string $url): array
    {
        $contentType = $response->getHeaderLine('Content-Type') ?: 'application/pdf';
        $filename = basename(parse_url($url, PHP_URL_PATH) ?: 'file.pdf');

        $outHeaders = [
            'Content-Type' => $contentType,
            'Content-Disposition' => "inline; filename=\"{$filename}\"",
            'X-Content-Type-Options' => 'nosniff',
        ];

        foreach (['Content-Length', 'Content-Range', 'Accept-Ranges', 'Last-Modified', 'ETag'] as $h) {
            if ($response->hasHeader($h)) {
                $outHeaders[$h] = $response->getHeaderLine($h);
            }
        }

        return $outHeaders;
    }

    /**
     * Handle PDF request errors
     */
    public function handlePdfError(Response $response, string $url, string $nim): array
    {
        $status = $response->getStatusCode();
        $bodyPreview = '[no body]';

        try {
            $bodyPreview = $response->getBody()->read(4096);
        } catch (\Throwable $e) {
            $bodyPreview = '[failed to read remote error body]';
        }

        Log::warning('Remote PDF returned error', [
            'url' => $url,
            'status' => $status,
            'body_preview' => $bodyPreview,
            'nim' => $nim,
        ]);

        return [
            'status' => $status,
            'body' => $bodyPreview ?: "Remote returned status $status",
            'content_type' => $response->getHeaderLine('Content-Type') ?: 'text/plain'
        ];
    }

    /**
     * Sanitize and decode URL with security checks
     */
    private function sanitizeAndDecodeUrl(string $url): ?string
    {
        $parsedUrl = parse_url($url);

        if (!$parsedUrl) {
            return null;
        }

        if (isset($parsedUrl['scheme'])) {
            if (isset($parsedUrl['path'])) {
                $decodedPath = urldecode($parsedUrl['path']);
                $parsedUrl['path'] = $decodedPath;
                return $this->unparseUrl($parsedUrl);
            }
            return $url;
        }

        $decodedPath = urldecode($url);
        $decodedPath = str_replace("\0", '', $decodedPath);
        $decodedPath = str_replace('\\', '/', $decodedPath);

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
    private function unparseUrl(array $parsed): string
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
}
