<?php

namespace Database\Seeders;

use App\Models\Fakultas;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UpdateKeteranganFakultasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $fakultasData = [
            'FITK' => 'ILMU TARBIYAH DAN KEGURUAN',
            'FEBI' => 'EKONOMI DAN BISNIS ISLAM',
            'FS' => 'SYARIAH',
            'FDKI' => 'DAKWAH DAN KOMUNIKASI ISLAM',
            'FUA' => 'USHULUDDIN DAN ADAB',
            'S2' => 'PASCASARJANA',
            'S3' => 'PASCASARJANA',
        ];

        foreach ($fakultasData as $kode => $keterangan) {
            Fakultas::where('nama_fakultas', $kode)
                ->update(['ket_fakultas' => $keterangan]);
        }
            
    }
}
