<?php

namespace Database\Seeders;

use App\Models\Prodi;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProdiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $prodi = [
            ['kode' => '101', 'nama_prodi' => 'Pendidikan Agama Islam', 'id_fakultas' => 1],
            ['kode' => '102', 'nama_prodi' => 'Pendidikan Bahasa Arab', 'id_fakultas' => 1],
            ['kode' => '103', 'nama_prodi' => 'Tadris Bahasa Inggris', 'id_fakultas' => 1],
            ['kode' => '104', 'nama_prodi' => 'Tadris Ilmu Pengetahuan Sosial', 'id_fakultas' => 1],
            ['kode' => '105', 'nama_prodi' => 'Tadris Matematika', 'id_fakultas' => 1],
            ['kode' => '106', 'nama_prodi' => 'Tadris Biologi', 'id_fakultas' => 1],
            ['kode' => '107', 'nama_prodi' => 'Pendidikan Guru Madrasah Ibtidaiyah', 'id_fakultas' => 1],
            ['kode' => '108', 'nama_prodi' => 'Pendidikan Islam Anak Usia Dini', 'id_fakultas' => 1],
            ['kode' => '109', 'nama_prodi' => 'Manajemen Pendidikan Islam', 'id_fakultas' => 1],
            ['kode' => '110', 'nama_prodi' => 'Tadris Bahasa Indonesia', 'id_fakultas' => 1],
            ['kode' => '111', 'nama_prodi' => 'Tadris Kimia', 'id_fakultas' => 1],
            ['kode' => '201', 'nama_prodi' => 'Hukum Keluarga/Akhwal Syaksiyah', 'id_fakultas' => 3],
            ['kode' => '202', 'nama_prodi' => 'Hukum Ekonomi Syariah/Muamalah', 'id_fakultas' => 3],
            ['kode' => '203', 'nama_prodi' => 'Perbankan Syariah', 'id_fakultas' => 2],
            ['kode' => '204', 'nama_prodi' => 'Ekonomi Syariah', 'id_fakultas' => 2],
            ['kode' => '205', 'nama_prodi' => 'Akuntansi Syariah', 'id_fakultas' => 2],
            ['kode' => '206', 'nama_prodi' => 'Hukum Tatanegara Islam', 'id_fakultas' => 3],
            ['kode' => '207', 'nama_prodi' => 'Ilmu Falak', 'id_fakultas' => 3],
            ['kode' => '208', 'nama_prodi' => 'Pariwisata Syariah', 'id_fakultas' => 2],
            ['kode' => '301', 'nama_prodi' => 'Sejarah Peradaban Islam', 'id_fakultas' => 5],
            ['kode' => '302', 'nama_prodi' => 'Komunikasi dan Penyiaran Islam', 'id_fakultas' => 4],
            ['kode' => '303', 'nama_prodi' => 'Aqidah dan Filsafat Islam', 'id_fakultas' => 5],
            ['kode' => '304', 'nama_prodi' => 'Ilmu Al-Quran dan Tafsir', 'id_fakultas' => 5],
            ['kode' => '305', 'nama_prodi' => 'Pengembangan Masyarakat Islam', 'id_fakultas' => 4],
            ['kode' => '306', 'nama_prodi' => 'Bimbingan dan Konseling Islam', 'id_fakultas' => 4],
            ['kode' => '307', 'nama_prodi' => 'Ilmu Hadis', 'id_fakultas' => 5],
            ['kode' => '308', 'nama_prodi' => 'Bahasa dan Sastra Arab', 'id_fakultas' => 5],
            ['kode' => '309', 'nama_prodi' => 'Sosiologi Agama', 'id_fakultas' => 4],
            ['kode' => '310', 'nama_prodi' => 'Tasawuf dan Psikoterapi', 'id_fakultas' => 5],
            ['kode' => '601', 'nama_prodi' => 'Manajemen Pendidikan Islam - S2', 'id_fakultas' => 6],
            ['kode' => '602', 'nama_prodi' => 'Pendidikan Agama Islam - S2', 'id_fakultas' => 6],
            ['kode' => '603', 'nama_prodi' => 'Pendidikan Agama Islam - S2', 'id_fakultas' => 6],
            ['kode' => '604', 'nama_prodi' => 'Hukum Keluarga (Akhwal Syaksiyah) - S2', 'id_fakultas' => 6],
            ['kode' => '605', 'nama_prodi' => 'Ekonomi Syariah - S2', 'id_fakultas' => 6],
            ['kode' => '701', 'nama_prodi' => 'Pendidikan Agama Islam - S3', 'id_fakultas' => 7],
            ['kode' => '702', 'nama_prodi' => 'Hukum Keluarga (Akhwal Syaksiyah) - S3', 'id_fakultas' => 7],
            ['kode' => '113', 'nama_prodi' => 'PJJ Pendidikan Agama Islam', 'id_fakultas' => 1],
            ['kode' => '211', 'nama_prodi' => 'Perbankan Syariah', 'id_fakultas' => 2],
            ['kode' => '212', 'nama_prodi' => 'Ekonomi Syariah', 'id_fakultas' => 2],
            ['kode' => '213', 'nama_prodi' => 'Akuntansi Syariah', 'id_fakultas' => 2],
            ['kode' => '214', 'nama_prodi' => 'Pariwisata Syariah', 'id_fakultas' => 2],
            ['kode' => '311', 'nama_prodi' => 'Hukum Keluarga (Akhwalul Syaksiyah)', 'id_fakultas' => 3],
            ['kode' => '312', 'nama_prodi' => 'Hukum Ekonomi Islam (Muamalah)', 'id_fakultas' => 3],
            ['kode' => '313', 'nama_prodi' => 'Hukum Tata Negara Islam', 'id_fakultas' => 3],
            ['kode' => '314', 'nama_prodi' => 'Ilmu Falak', 'id_fakultas' => 3],
            ['kode' => '411', 'nama_prodi' => 'Komunikasi dan Penyiaran Islam', 'id_fakultas' => 4],
            ['kode' => '412', 'nama_prodi' => 'Pengembangan Masyarakat Islam', 'id_fakultas' => 4],
            ['kode' => '413', 'nama_prodi' => 'Bimbingan Konseling Islam', 'id_fakultas' => 4],
            ['kode' => '414', 'nama_prodi' => 'Sosiologi Agama', 'id_fakultas' => 4],
            ['kode' => '511', 'nama_prodi' => 'Sejarah Peradaban Islam', 'id_fakultas' => 5],
            ['kode' => '512', 'nama_prodi' => 'Akidah dan Filsafat Islam', 'id_fakultas' => 5],
            ['kode' => '513', 'nama_prodi' => 'Ilmu Al Quran dan Tafsir', 'id_fakultas' => 5],
            ['kode' => '514', 'nama_prodi' => 'Ilmu Hadis', 'id_fakultas' => 5],
            ['kode' => '515', 'nama_prodi' => 'Bahasa dan Sastra Arab', 'id_fakultas' => 5],
            ['kode' => '516', 'nama_prodi' => 'Tasawuf dan Psikoterapi', 'id_fakultas' => 5],
            ['kode' => '606', 'nama_prodi' => 'Pengembangan Masyarakat Islam - S2', 'id_fakultas' => 6]
        ];

        foreach ($prodi as $key => $value) {
            Prodi::create([
                'id_fakultas' => $value['id_fakultas'],
                'kode' => $value['kode'],
                'nama_prodi' => $value['nama']
            ]);
        }
    }
}
