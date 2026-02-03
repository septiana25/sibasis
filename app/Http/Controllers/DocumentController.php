<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Services\DocumentService;
use App\Services\MahasiswaApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DocumentController extends Controller
{


    public function __construct(
        protected DocumentService $documentService,
        protected MahasiswaApiService $mahasiswaApiService
    ) {}

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
        $validated = $request->validate($this->documentService->getValidationRules());
        try {
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

    public function edit(Document $document)
    {
        return response()->json([
            'id' => $document->id,
            'judul' => $document->judul,
            'tahun' => $document->tahun,
            'file_path' => $document->file_path
        ]);
    }

    public function update(Request $request, Document $document)
    {
        $validated = $request->validate($this->documentService->getUpdateValidationRules($document));
        
        try {
            $updatedDocument = $this->documentService->updateDocument($document, $validated);

            return response()->json([
                'success' => true,
                'message' => 'Dokumen berhasil diperbarui.',
                'document' => [
                    'id' => $updatedDocument->id,
                    'judul' => $updatedDocument->judul,
                    'tahun' => $updatedDocument->tahun,
                    'file_path' => $updatedDocument->file_path
                ]
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Document update failed', [
                'error' => $e->getMessage(),
                'document_id' => $document->id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui dokumen. Silakan coba lagi.'
            ], 500);
        }
    }

    public function lookupByNim(Request $request)
    {
        $request->validate([
            'nim' => 'required|string',
        ]);

        try {
            $nim = $request->input('nim');
            $studentData = $this->mahasiswaApiService->getDataMahasiswaAPI($nim);
            //$studentData = $this->mahasiswaApiService->lookupByNim($nim);

            return response()->json($studentData);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 404);
        }
    }
}
