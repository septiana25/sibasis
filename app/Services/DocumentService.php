<?php

namespace App\Services;

use App\Models\Document;
use App\Models\Fakultas;
use App\Models\Prodi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DocumentService
{

    public function __construct(protected SkripsiApiService $skripsiApiService) {}
    /**
     * Get paginated documents
     */
    public function getPaginatedDocuments(int $perPage = 15, array $filters = [])
    {
        $query = Document::query()->with(['fakultasRelation', 'prodiRelation']);

        // Apply search filters
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('nim', 'like', "%{$search}%")
                    ->orWhere('nama', 'like', "%{$search}%")
                    ->orWhere('fakultas', 'like', "%{$search}%")
                    ->orWhere('prodi', 'like', "%{$search}%")
                    ->orWhere('judul', 'like', "%{$search}%");
            });
        }

        // Apply status filter
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Apply year filter
        if (!empty($filters['tahun'])) {
            $query->where('tahun', $filters['tahun']);
        }

        // Apply fakultas filter
        if (!empty($filters['fakultas'])) {
            $query->where('fakultas', 'like', "%{$filters['fakultas']}%");
        }

        // Apply date range filter
        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        return $data = $query->orderByDesc('created_at')->paginate($perPage);
    }

    /**
     * Get available years from documents
     */
    public function getAvailableYears(): array
    {
        return Document::distinct('tahun')
            ->orderBy('tahun', 'desc')
            ->pluck('tahun')
            ->toArray();
    }

    /**
     * Get available statuses
     */
    public function getAvailableStatuses(): array
    {
        return [
            'pending' => 'Pending',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            'padding' => 'Padding'
        ];
    }

    /**
     * Get available fakultas from documents
     */
    public function getAvailableFakultas(): array
    {
        $fakultasIds = Document::distinct('fakultas')->pluck('fakultas');

        return Fakultas::whereIn('id', $fakultasIds)
            ->orderBy('ket_fakultas')
            ->get(['id', 'nama_fakultas', 'ket_fakultas'])
            ->toArray();
    }

    /**
     * Create a new document record and store the file
     */
    public function createDocument(array $validatedData): Document
    {
        $nim = $validatedData['nim'];
        $nama = strtoupper($validatedData['nama']);
        $judul = strtoupper($validatedData['judul']);
        $tahun = (int) $validatedData['tahun'];
        $prodi = $validatedData['prodi'];
        $prodi_kode = $validatedData['prodi_kode'];
        $email = $validatedData['email'] ?? null;
        $no_hp = $validatedData['no_hp'] ?? null;
        $alamat = $validatedData['alamat'] ?? null;
        $file = $validatedData['file'];

        $nimExistsFromSkripsiApi = $this->skripsiApiService->getSkripsiFromApi(['nim' => $nim]);

        if ($nimExistsFromSkripsiApi) {
            throw new \InvalidArgumentException('NIM sudah ada di SIBASKA');
        }
        $matchProdi = $this->getProdiByKode($prodi_kode);
        if (!$matchProdi) {
            throw new \InvalidArgumentException('Program Studi tidak ditemukan');
        }

        $filePath = $this->storeDocumentFile($file, $nim, $tahun, $matchProdi['fakultas_kode'], $matchProdi['nama_prodi']);

        return Document::create([
            'nim' => $nim,
            'nama' => $nama,
            'judul' => $judul,
            'fakultas' => $matchProdi['id_fakultas'],
            'prodi' => $matchProdi['id_prodi'],
            'tahun' => $tahun,
            'email' => $email,
            'no_hp' => $no_hp,
            'alamat' => $alamat,
            'file_path' => $filePath,
            'status' => 'pending',
            'uploaded_by' => Auth::id(),
        ]);
    }

    function getProdiByKode($prodiKode)
    {

        $data = Prodi::with('fakultas')
            ->where('kode', $prodiKode)
            ->first();
        if ($data) {
            return [
                'id_prodi' => $data->id,
                'nama_prodi' => $data->nama_prodi,
                'prodi_kode' => $data->kode,
                'id_fakultas' => $data->id_fakultas,
                'fakultas' => $data->fakultas->ket_fakultas,
                'fakultas_kode' => $data->fakultas->nama_fakultas,
            ];
        }
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
                'id',
                'nim',
                'nama',
                'fakultas',
                'prodi',
                'tahun',
                'file_path',
                'status',
                'uploaded_by',
                'created_at'
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
     * Update document with limited fields (judul, file_path, tahun)
     */
    public function updateDocument(Document $document, array $validatedData): Document
    {
        $judul = strtoupper($validatedData['judul']);
        $tahun = (int) $validatedData['tahun'];

        $updateData = [
            'judul' => $judul,
            'tahun' => $tahun,
        ];

        // Handle file update if provided
        if (isset($validatedData['file'])) {
            $file = $validatedData['file'];

            // Get existing document details for file path structure
            $fakultas = $document->fakultasRelation;
            $prodi = $document->prodiRelation;

            // Delete old file if it exists
            if (Storage::disk('public')->exists($document->file_path)) {
                Storage::disk('public')->delete($document->file_path);
            }

            // Store new file
            $filePath = $this->storeDocumentFile(
                $file,
                $document->nim,
                $tahun,
                $fakultas->nama_fakultas,
                $prodi->nama_prodi
            );

            $updateData['file_path'] = $filePath;
        }

        $document->update($updateData);

        return $document->fresh();
    }

    /**
     * Get validation rules for document update
     */
    public function getUpdateValidationRules(Document $document): array
    {
        return [
            'judul' => 'required|string|unique:documents,judul,' . $document->id,
            'tahun' => 'required|numeric|min:1900|max:' . (date('Y') + 5),
            'file' => 'nullable|file|mimes:pdf|max:10240',
        ];
    }

    /**
     * Validate document creation request
     */
    public function getValidationRules(): array
    {
        return [
            'nim' => 'required|string|unique:documents,nim',
            'judul' => 'required|string|unique:documents,judul',
            'tahun' => 'required|numeric',
            'nama' => 'required|string',
            'fakultas' => 'required|string',
            'prodi' => 'required|string',
            'prodi_kode' => 'required|string',
            'email' => 'nullable|email',
            'no_hp' => 'nullable|string',
            'alamat' => 'nullable|string',
            'file' => 'required|file|mimes:pdf|max:10240',
        ];
    }
}
