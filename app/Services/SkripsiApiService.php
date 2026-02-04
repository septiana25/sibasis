<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class SkripsiApiService
{

    public function getSkripsiFromApi(array $queryParams)
    {
        $client = new Client();
        $url = env('API_URL');

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
                Log::info("API returned empty data section.", ['queryParams' => $queryParams]);
                return null;
            }

            return $data;
        } catch (\Exception $e) {
            // ✅ Tangani error seperti timeout, 404, koneksi gagal
            Log::error("API error: " . $e->getMessage());
            return null;
        }
    }
}
