<?php
namespace App\Services;

use App\Models\Bookmark;
use App\Models\SearchStatistic;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class SkripsiService{

    public function getDropdown(): array {
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
    
        $defaultProdi = [
            ['index' => 100, 'id' => 101, 'nama' => 'Pendidikan Agama Islam'],
            ['index' => 100, 'id' => 102, 'nama' => 'Pendidikan Bahasa Arab'],
            ['index' => 100, 'id' => 103, 'nama' => 'Tadris Bahasa Inggris'],
            ['index' => 100, 'id' => 104, 'nama' => 'Tadris Ilmu Pengetahuan'],
            ['index' => 100, 'id' => 105, 'nama' => 'Tadris Matematika'],
            ['index' => 100, 'id' => 106, 'nama' => 'Tadris Biologi'],
            ['index' => 100, 'id' => 107, 'nama' => 'Pendidikan Guru Madrasah Ibtidaiyah'],
            ['index' => 100, 'id' => 108, 'nama' => 'Pendidikan Islam Anak Usia Dini'],
            ['index' => 100, 'id' => 109, 'nama' => 'Manajemen Pendidikan Islam'],
            ['index' => 100, 'id' => 110, 'nama' => 'Tadris Bahasa Indonesia'],
            ['index' => 100, 'id' => 111, 'nama' => 'Tadris Kimia'],
            ['index' => 511, 'id' => 111, 'nama' => 'Sejarah Peradaban Islam'],
            ['index' => 301, 'id' => 111, 'nama' => 'Sejarah Peradaban Islam'],
            ['index' => 512, 'id' => 111, 'nama' => 'Akidah dan Filsafat Islam'],
        ];

        $defaultYear = ['year' => 2025, 'year' => 2024, 'year' => 2023, 'year' => 2022, 'year' => 2021];

        return [$defaultFakultas, $defaultProdi, $defaultYear];
    } 

    public function searchSkripsiFromApi(array $queryParams)
    {
        $client = new Client();
        $url = "https://opac.uinssc.ac.id/skbpp/api/get-dokumen";

        try {
            $response = $client->get($url, [
                'query' => $queryParams
            ]);

            
            // ✅ Cek status code
            if ($response->getStatusCode() !== 200) {
                Log::warning("API returned non-200 status: " . $response->getStatusCode());
                return null;
            }
            
            // ✅ Ambil body dan decode
            $body = (string) $response->getBody();
            $data = json_decode($body, true);

    
            // ✅ Validasi apakah isi data sesuai
            if (
                !is_array($data) ||                          // respon bukan array
                !array_key_exists('data', $data) ||          // tidak punya key 'data'
                !is_array($data['data']) ||                  // key 'data' bukan array
                empty($data['data'])                         // array 'data' tapi kosong
            ) {
                Log::warning("API returned empty or invalid JSON structure.");
                Log::info("API returned empty data section.", ['queryParams' => $queryParams, 'rawResponse' => $data]);
                return null;
            }
    
            return $data;
    
        } catch (\Exception $e) {
            // ✅ Tangani error seperti timeout, 404, koneksi gagal
            Log::error("API error: " . $e->getMessage());
            return null;
        }
    }

    public function getDocumentFromSession($index, array $dataSession): array
    {

        if (!$dataSession || !isset($dataSession['data']) || !is_array($dataSession['data'])) {
            abort(404, 'Sesi data skripsi tidak ditemukan atau telah kedaluwarsa.');
        }

        // 3) Validasi index
        if (!array_key_exists($index, $dataSession['data'])) {
            abort(404, 'Index data tidak valid.');
        }

        $detail = $dataSession['data'][$index];

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
            Log::error($validator);
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

    public function toggleBookmark(int $index, array $dataSession, object $user): string
    {
        // Pastikan struktur minimal ada
        if (!$dataSession || !isset($dataSession['data']) || !is_array($dataSession['data'])) {
            return 'data tidak ada';
        }

        // Pastikan index ada
        if (!array_key_exists($index, $dataSession['data'])) {
            return 'index tidak ada';
        }

        [$detail, $fileInfo] = $this->getDocumentFromSession($index, $dataSession);

        $bookmark = $this->getBookmark($user->member_id, $detail);

        if ($bookmark) {
            // jika sudah ada, hapus (unbookmark)
            $bookmark->delete();
            $this->setBookmarkFlagInSession($index, false, $dataSession);
            return 'Bookmark dihapus.';
        }

        Bookmark::create([
            'member_id' => $user->member_id,
            'nim' => $detail['nim'],
            'member_name' => $user->member_name ?? null,
            'judul_ta' => $detail['judul_ta'],
            'meta' => [
                'saved_at_session_index' => $index,
            ],
        ]);

        $this->setBookmarkFlagInSession($index, true, $dataSession);
        return 'Berhasil ditambahkan ke bookmark.';
    }

    private function setBookmarkFlagInSession(int $index, bool $value, array $dataSession): void
    {
        // Pastikan struktur minimal ada
        if (!$dataSession || !isset($dataSession['data']) || !is_array($dataSession['data'])) {
            return;
        }
        
        // Pastikan index ada
        if (!array_key_exists($index, $dataSession['data'])) {
            return;
        }
        
        // Set flag 'bookmarked'
        $dataSession['data'][$index]['bookmarked'] = $value;

        // Simpan kembali ke session
        session(['skripsi_results' => $dataSession]);
    }

    public function getBookmarkByMember(int $member_id, array $detail)
    {
        if (isset($detail['bookmarked']) && $detail['bookmarked'] ) return true;
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

    
}