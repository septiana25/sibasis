<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MahasiswaApiService
{
    protected string $baseUrl;
    protected string $token;
    protected int $timeout;

    public function __construct()
    {
        $this->baseUrl = config('services.mahasiswa_api.base_url', '');
        $this->token = config('services.mahasiswa_api.token', '');
        $this->timeout = config('services.mahasiswa_api.timeout', 10);
    }

    /**
     * Check if API is properly configured
     */
    public function isConfigured(): bool
    {
        return !empty($this->baseUrl) && !empty($this->token);
    }

    /**
     * Lookup student data by NIM
     */

    public function getDataMahasiswaAPI(string $nim)
    {
        // $mahasiswa = Mahasiswa::where('nim', $req->nim)->pluck('nama', 'kodejurusan');
        $api_url = rtrim($this->baseUrl, '/') . "/internal/sibaska/student?nim={$nim}"; 
        $api_header = ['Authorization' => 'Bearer ' . $this->token];
        $mahasiswa = json_decode(Http::withHeaders($api_header)->get($api_url))->data;
        return $mahasiswa;
    }
    public function lookupByNim(string $nim): array
    {
        if (!$this->isConfigured()) {
            throw new \Exception('API mahasiswa tidak dikonfigurasi');
        }

        $url = rtrim($this->baseUrl, '/') . '/mahasiswa/filter';
        
        try {
            $response = Http::withToken($this->token)
                ->asForm()
                ->timeout($this->timeout)
                ->post($url, ['nim' => $nim]);

            return $this->processApiResponse($response, $nim, $url);
            
        } catch (\Exception $e) {
            Log::error('Mahasiswa API request failed', [
                'nim' => $nim,
                'error' => $e->getMessage(),
                'url' => $url
            ]);
            
            throw new \Exception('Gagal menghubungi API mahasiswa: ' . $e->getMessage());
        }
    }

    /**
     * Process API response and normalize data structure
     */
    protected function processApiResponse($response, string $nim, string $url): array
    {
        // Try to decode JSON regardless of HTTP status
        $data = null;
        try {
            $data = $response->json();
        } catch (\Throwable $e) {
            Log::warning('Failed to decode API response', [
                'nim' => $nim,
                'status' => $response->status(),
                'body' => $response->body()
            ]);
        }

        // Some APIs mistakenly return 404 with a success payload. Trust payload when status=true.
        if ($response->failed() && !($data && ($data['status'] ?? false))) {
            Log::warning('Mahasiswa API request failed', [
                'url' => $url,
                'nim' => $nim,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            
            throw new \Exception('Data mahasiswa tidak ditemukan');
        }

        return $this->normalizeStudentData($data);
    }

    /**
     * Normalize student data structure from API response
     */
    protected function normalizeStudentData($data): array
    {
        // Handle different response structures
        if (isset($data['data'])) {
            $studentData = is_array($data['data']) && isset($data['data'][0]) 
                ? $data['data'][0] 
                : $data['data'];
        } elseif (is_array($data) && isset($data[0])) {
            $studentData = $data[0];
        } else {
            $studentData = $data ?? [];
        }

        return [
            'nama' => $studentData['nama'] ?? null,
            'fakultas' => $studentData['fakultas'] ?? null,
            'prodi' => $studentData['prodi'] ?? null,
            'prodi_kode' => $studentData['prodi_kode'] ?? null,
            'jenjang' => $studentData['jenjang'] ?? null,
            'no_hp' => $studentData['no_hp'] ?? null,
            'email' => $studentData['email'] ?? null,
            'tempat_lahir' => $studentData['tempat_lahir'] ?? null,
        ];
    }

    /**
     * Get API configuration status for debugging
     */
    public function getStatus(): array
    {
        return [
            'configured' => $this->isConfigured(),
            'base_url' => $this->baseUrl ? 'Set' : 'Missing',
            'token' => $this->token ? 'Set' : 'Missing',
            'timeout' => $this->timeout,
        ];
    }
}