<?php

namespace Database\Seeders;

use App\Models\Fakultas;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FakultasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaultFakultas = [
            ['id_fakultas' => 1, 'nama_fakultas' => 'FITK'], 
            ['id_fakultas' => 2, 'nama_fakultas' => 'FEBI'], 
            ['id_fakultas' => 3, 'nama_fakultas' => 'FS'], 
            ['id_fakultas' => 4, 'nama_fakultas' => 'FDKI'], 
            ['id_fakultas' => 5, 'nama_fakultas' => 'FUA'], 
            ['id_fakultas' => 6, 'nama_fakultas' =>  'S2'],
            ['id_fakultas' => 7, 'nama_fakultas' =>  'S3'],
        ];

        foreach ($defaultFakultas as $key => $value) {
            Fakultas::create([
                'nama_fakultas' => $value['nama_fakultas']
            ]);
        }
        
    }
}
