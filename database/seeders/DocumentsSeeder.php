<?php

namespace Database\Seeders;

use App\Models\Document;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DocumentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /* for ($i = 1; $i <= 500; $i++) {
            Document::create([
                'nim' => '202100' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'nama' => 'Mahasiswa ' . $i,
                'fakultas' => 1,
                'prodi' => 1,
                'judul' => 'Judul Dokumen ' . $i,
                'tahun' => 2021,
                'status' => 'pending',
                'file_path' => 'documents/sample.pdf',
                'uploaded_by' => 4,
            ]);
        } */
    }
}
