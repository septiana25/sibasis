<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Services\DocumentService;
use App\Services\MahasiswaApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DocumentController extends Controller
{
    protected DocumentService $documentService;
    protected MahasiswaApiService $mahasiswaApiService;

    public function __construct(
        DocumentService $documentService,
        MahasiswaApiService $mahasiswaApiService
    ) {
        $this->documentService = $documentService;
        $this->mahasiswaApiService = $mahasiswaApiService;
    }

    /* public function __construct()
    {
        $this->middleware('auth:web');
        // Optional: gate by role if available in future
    } */

    public function index(Request $request)
    {
        $filters = $this->buildFilters($request);
        $perPage = $request->get('per_page', 15);
        
        $documents = $this->documentService->getPaginatedDocuments($perPage, $filters);
        
        // Get filter options for dropdowns
        $filterOptions = [
            'years' => $this->documentService->getAvailableYears(),
            'statuses' => $this->documentService->getAvailableStatuses(),
            'fakultas' => $this->documentService->getAvailableFakultas(),
        ];
        
        // Preserve query parameters in pagination links
        $documents->appends($request->query());
        
        return view('documents.index', compact('documents', 'filterOptions', 'filters'));
    }

    /**
     * Build filters array from request parameters
     */
    protected function buildFilters(Request $request): array
    {
        return [
            'search' => $request->get('search'),
            'status' => $request->get('status'),
            'tahun' => $request->get('tahun'),
            'fakultas' => $request->get('fakultas'),
            'date_from' => $request->get('date_from'),
            'date_to' => $request->get('date_to'),
        ];
    }

    public function create()
    {
        return view('documents.create');
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate($this->documentService->getValidationRules());
            $this->documentService->createDocument($validated);
            
            return redirect()->route('admin.documents.create')
                ->with('success', 'Dokumen berhasil diupload.');
                
        } catch (\InvalidArgumentException $e) {
            return back()->withInput()
                ->withErrors(['fakultas' => $e->getMessage()]);
                
        } catch (\Exception $e) {
            Log::error('Document creation failed', [
                'error' => $e->getMessage(),
                'nim' => $request->input('nim')
            ]);
            
            return back()->withInput()
                ->withErrors(['error' => 'Gagal menyimpan dokumen. Silakan coba lagi.']);
        }
    }

    public function show(Document $document)
    {
        try {
            return $this->documentService->streamDocument($document);
        } catch (\Exception $e) {
            Log::error('Document streaming failed', [
                'document_id' => $document->id,
                'file_path' => $document->file_path,
                'error' => $e->getMessage()
            ]);
            
            abort(404, 'Dokumen tidak ditemukan');
        }
    }

    public function export()
    {
        return $this->documentService->exportToCsv();
    }

    public function lookupByNim(Request $request)
    {
        $request->validate([
            'nim' => 'required|string',
        ]);

        try {
            $nim = $request->input('nim');
            $studentData = $this->mahasiswaApiService->lookupByNim($nim);
            
            return response()->json($studentData);
            
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 404);
        }
    }

}


