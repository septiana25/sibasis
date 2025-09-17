<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Facades\Log;

class DocumentController extends Controller
{
    /* public function __construct()
    {
        $this->middleware('auth:web');
        // Optional: gate by role if available in future
    } */

    public function index()
    {
        $documents = Document::orderByDesc('created_at')->paginate(15);
        return view('documents.index', compact('documents'));
    }

    public function create()
    {
        return view('documents.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nim' => 'required|string|unique:documents,nim',
            'tahun' => 'required|numeric',
            'nama' => 'required|string',
            'fakultas' => 'required|string',
            'prodi' => 'required|string',
            'file' => 'required|file|mimes:pdf|max:10240',
        ]);

        $nim = $validated['nim'];
        $tahun = (int) $validated['tahun'];
        $fakultas = $validated['fakultas'];
        $prodi = $validated['prodi'];

        $storagePath = "{$tahun}/{$fakultas}/{$prodi}/{$nim}";
        $filename = $nim . '_fulltext.pdf';
        $disk = 'public';

        // Ensure directory exists
        Storage::disk($disk)->makeDirectory($storagePath);

        // Store file
        Storage::disk($disk)->putFileAs($storagePath, $validated['file'], $filename);
        $filePath = $storagePath . '/' . $filename;

        Document::create([
            'nim' => $nim,
            'nama' => $validated['nama'],
            'fakultas' => $fakultas,
            'prodi' => $prodi,
            'tahun' => $tahun,
            'file_path' => $filePath,
            'status' => 'padding',
            'uploaded_by' => Auth::id(),
        ]);

        return redirect()->route('admin.documents.index')->with('success', 'Dokumen berhasil diupload.');
    }

    public function show(Document $document)
    {
        // Stream the PDF
        $disk = 'public';
        if (!Storage::disk($disk)->exists($document->file_path)) {
            abort(404);
        }
        $stream = Storage::disk($disk)->readStream($document->file_path);
        return new StreamedResponse(function () use ($stream) {
            fpassthru($stream);
        }, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . basename($document->file_path) . '"',
        ]);
    }

    public function export()
    {
        $filename = 'documents_export_' . now()->format('Ymd_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['id', 'nim', 'nama', 'fakultas', 'prodi', 'tahun', 'file_path', 'status', 'uploaded_by', 'created_at']);
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

    public function lookupByNim(Request $request)
    {
        $request->validate([
            'nim' => 'required|string',
        ]);

        $nim = $request->input('nim');
        $baseUrl = config('services.mahasiswa_api.base_url');
        $token = config('services.mahasiswa_api.token');

        if (!$baseUrl || !$token) {
            return response()->json(['message' => 'API not configured'], 500);
        }

        $url = rtrim($baseUrl, '/') . '/mahasiswa/filter';
        $response = Http::withToken($token)
            ->asForm()
            ->timeout(10)
            ->post($url, ['nim' => $nim]);

        // Try to decode JSON regardless of HTTP status
        $data = null;
        try {
            $data = $response->json();
        } catch (\Throwable $e) {
            // ignore decode error; will handle as failure below
        }

        // Some APIs mistakenly return 404 with a success payload. Trust payload when status=true.
        if ($response->failed() && !($data && ($data['status'] ?? false))) {
            Log::warning('Mahasiswa filter failed', [
                'url' => $url,
                'nim' => $nim,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            return response()->json(['message' => 'Data tidak ditemukan'], $response->status());
        }

        // Normalize expected fields
        if (isset($data['data'])) {
            $data = is_array($data['data']) && isset($data['data'][0]) ? $data['data'][0] : $data['data'];
        } elseif (is_array($data) && isset($data[0])) {
            $data = $data[0];
        }

        return response()->json([
            'nama' => $data['nama'] ?? $data['name'] ?? null,
            'fakultas' => $data['fakultas'] ?? $data['faculty'] ?? null,
            'prodi' => $data['prodi'] ?? $data['program_studi'] ?? $data['study_program'] ?? null,
        ]);
    }
}


