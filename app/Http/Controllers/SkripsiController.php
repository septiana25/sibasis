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


    public function __construct(protected SkripsiService $skripsiService)
    {
        // Inisialisasi Guzzle client jika belum di-inject
        $this->httpClient = new Client([
            'timeout' => 60,
            'connect_timeout' => 10,
            // 'verify' => false, // jangan pakai di production
        ]);
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
        try {
            // Prepare query parameters
            $queryParams = $this->skripsiService->prepareQueryParams($request);

            // Handle complete search process
            $searchData = $this->skripsiService->handleSearchProcess($queryParams);

            // Get popular keywords for view
            $popular = $this->skripsiService->getTopSearchStatistics();

            return view('skripsi.index', [
                'results' => $searchData['results'],
                'popularKeywords' => $popular,
                'fakultasList' => $searchData['fakultasList'],
                'yearList' => $searchData['yearList']
            ]);
        } catch (\Exception $e) {
            session(['skripsi_results' => []]);
            return redirect()->route('skripsi.index')->with('error', $e->getMessage());
        }
    }

    public function show($nim)
    {
        try {
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
        } catch (\Exception $e) {
            return redirect()->route('skripsi.index')->with('error', $e->getMessage());
        }
    }

    /**
     * Stream PDF file from remote source with range support
     */
    public function streamPdf(Request $request, $nim)
    {
        try {
            // Prepare PDF streaming data
            $pdfData = $this->skripsiService->preparePdfStream($nim);
            $url = $pdfData['url'];

            // Prepare HTTP options
            $options = $this->skripsiService->preparePdfHttpOptions($request);

            // Make HTTP request
            $response = $this->httpClient->request('GET', $url, $options);
            $status = $response->getStatusCode();

            // Handle errors
            if ($status >= 400) {
                $errorData = $this->skripsiService->handlePdfError($response, $url, $nim);
                return response($errorData['body'], $errorData['status'])
                    ->header('Content-Type', $errorData['content_type']);
            }

            // Prepare response headers
            $headers = $this->skripsiService->preparePdfHeaders($response, $url);

            // Create streaming response
            $body = $response->getBody();
            $streamed = new StreamedResponse(function () use ($body) {
                while (!$body->eof()) {
                    echo $body->read(1024 * 8);
                    if (function_exists('ob_flush')) {
                        @ob_flush();
                    }
                    flush();
                }
            }, $status, $headers);

            return $streamed;
        } catch (\Throwable $e) {
            if ($e->getCode() === 404 || $e->getCode() === 400) {
                return response($e->getMessage(), $e->getCode());
            }

            Log::error('PDF streaming error', [
                'nim' => $nim,
                'exception' => $e->getMessage(),
            ]);

            return response('Gagal menghubungi sumber file (gateway error).', 502);
        }
    }

    public function toggleBookmark(Request $request, $nim)
    {
        $dataSession = session('skripsi_results');
        if (empty($dataSession)) {
            return redirect()->route('skripsi.index')->with('success', 'session data habis');
        }

        $user = Auth::guard('member')->user();

        $message = $this->skripsiService->toggleBookmark($nim, $dataSession, $user);

        return redirect()->back()->with('success', $message);
    }
}
