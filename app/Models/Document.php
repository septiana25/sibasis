<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'nim',
        'nama',
        'judul',
        'fakultas',
        'prodi',
        'tahun',
        'email',
        'no_hp',
        'alamat',
        'file_path',
        'status',
        'uploaded_by',
    ];

    public function fakultasRelation()
    {
        return $this->belongsTo(Fakultas::class, 'fakultas');
    }
    public function prodiRelation()
    {
        return $this->belongsTo(Prodi::class, 'prodi');
    }
}


