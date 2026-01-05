<?php

namespace App\Services;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DocumentService
{
    protected FakultasService $fakultasService;

    public function __construct(FakultasService $fakultasService)
    {
        $this->fakultasService = $fakultasService;
    }

    /**
     * Get paginated documents
     */
    public function getPaginatedDocuments(int $perPage = 15)
    {
        return Document::orderByDesc('created_at')->paginate($perPage);
    }

    /**
     * Create a new document record and store the file
     */
    public function createDocument(array $validatedData): Document
    {
        $nim = $validatedData['nim'];
        $tahun = (int) $validatedData['tahun'];
        $fakultas = $validatedData['fakultas'];
        $prodi = $validatedData['prodi'];
        $file = $validatedData['file'];

        $matchFakultas = $this->fakultasService->getFakulByFakultas($fakultas);
        
        if (!$matchFakultas) {
            throw new \InvalidArgumentException('Fakultas tidak ditemukan');
        }

        $filePath = $this->storeDocumentFile($file, $nim, $tahun, $matchFakultas['fakul'], $prodi);

        return Document::create([
            'nim' => $nim,
            'nama' => $validatedData['nama'],
            'fakultas' => $fakultas,
            'prodi' => $prodi,
            'tahun' => $tahun,
            'file_path' => $filePath,
            'status' => 'padding',
            'uploaded_by' => Auth::id(),
        ]);
    }

    /**
     * Store document file in organized directory structure
     */
    protected function storeDocumentFile($file, string $nim, int $tahun, string $fakultasCode, string $prodi): string
    {
        $storagePath = "document/{$tahun}/{$fakultasCode}/{$prodi}/{$nim}";
        $filename = $nim . '_9_fulltext.pdf';
        $disk = 'public';

        // Ensure directory exists
        Storage::disk($disk)->makeDirectory($storagePath);

        // Store file
        Storage::disk($disk)->putFileAs($storagePath, $file, $filename);
        
        return $storagePath . '/' . $filename;
    }

    /**
     * Stream document PDF for viewing
     */
    public function streamDocument(Document $document): StreamedResponse
    {
        $disk = 'public';
        
        if (!Storage::disk($disk)->exists($document->file_path)) {
            throw new \Exception('File tidak ditemukan');
        }

        $stream = Storage::disk($disk)->readStream($document->file_path);
        
        return new StreamedResponse(function () use ($stream) {
            fpassthru($stream);
        }, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . basename($document->file_path) . '"',
        ]);
    }

    /**
     * Export documents to CSV
     */
    public function exportToCsv(): StreamedResponse
    {
        $filename = 'documents_export_' . now()->format('Ymd_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () {
            $handle = fopen('php://output', 'w');
            
            // Write header row
            fputcsv($handle, [
                'id', 'nim', 'nama', 'fakultas', 'prodi', 
                'tahun', 'file_path', 'status', 'uploaded_by', 'created_at'
            ]);
            
            // Write data rows in chunks for memory efficiency
            Document::orderBy('id')->chunk(500, function ($rows) use ($handle) {
                foreach ($rows as $row) {
                    fputcsv($handle, [
                        $row->id,
                        $row->nim,
                        $row->nama,
                        $row->fakultas,
                        $row->prodi,
                        $row->tahun,
                        $row->file_path,
                        $row->status,
                        $row->uploaded_by,
                        optional($row->created_at)->toDateTimeString(),
                    ]);
                }
            });
            
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Validate document creation request
     */
    public function getValidationRules(): array
    {
        return [
            'nim' => 'required|string|unique:documents,nim',
            'tahun' => 'required|numeric',
            'nama' => 'required|string',
            'fakultas' => 'required|string',
            'prodi' => 'required|string',
            'file' => 'required|file|mimes:pdf|max:10240',
        ];
    }
}