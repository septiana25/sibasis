@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Upload Dokumen PDF</h3>
        <a href="{{ route('admin.documents.index') }}" class="btn btn-light">Kembali</a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.documents.store') }}" method="POST" enctype="multipart/form-data" id="upload-form">
                @csrf
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="nim" class="form-label">NIM</label>
                        <div class="input-group">
                            <input type="text" name="nim" id="nim" class="form-control" value="{{ old('nim') }}" required>
                            <button class="btn btn-outline-secondary" type="button" id="btn-cek">Cek</button>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <label for="nama" class="form-label">Nama</label>
                        <input type="text" name="nama" id="nama" class="form-control" value="{{ old('nama') }}" readonly required>
                    </div>

                    <div class="col-md-4">
                        <label for="hp" class="form-label">No Hp</label>
                        <input type="text" name="hp" id="hp" class="form-control" value="{{ old('no_hp') }}" readonly required>
                    </div>

                    <div class="col-md-4">
                        <label for="email" class="form-label">Email</label>
                        <input type="text" name="email" id="email" class="form-control" value="{{ old('email') }}" readonly required>
                    </div>

                    <div class="col-md-4">
                        <label for="tempat_lahir" class="form-label">Tempat Lahir</label>
                        <input type="text" name="tempat_lahir" id="tempat_lahir" class="form-control" value="{{ old('tempat_lahir') }}" readonly required>
                    </div>
                    
                    <div class="col-md-8">
                        <label for="tahun" class="form-label">Judul Skripsi</label>
                        <input type="number" name="tahun" id="tahun" class="form-control" value="{{ old('tahun') }}" required>
                    </div>

                    <div class="col-md-4">
                        <label for="tahun" class="form-label">Tahun Skripsi</label>
                        <input type="number" name="tahun" id="tahun" class="form-control" value="{{ old('tahun') }}" required>
                    </div>

                    <div class="col-md-6">
                        <label for="fakultas" class="form-label">Fakultas</label>
                        <input type="text" name="fakultas" id="fakultas" class="form-control" value="{{ old('fakultas') }}" readonly required>
                    </div>

                    <div class="col-md-6">
                        <label for="prodi" class="form-label">Prodi</label>
                        <input type="text" name="prodi" id="prodi" class="form-control" value="{{ old('prodi') }}" readonly required>
                    </div>

                    <div class="col-12">
                        <label for="file" class="form-label">File PDF (max 10MB)</label>
                        <input type="file" name="file" id="file" accept="application/pdf" class="form-control" required>
                    </div>
                    <input type="text" name="prodi_kode" id="prodi_kode" value="{{ old('prodi_kode') }}">
                    <input type="text" name="jenjang" id="jenjang" value="{{ old('jenjang') }}">
                </div>
                <div class="mt-3">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('btn-cek').addEventListener('click', async function() {
    const nim = document.getElementById('nim').value.trim();
    if (!nim) return;
    const url = `{{ route('admin.documents.lookup') }}`;
    try {
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const res = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ nim })
        });
        if (!res.ok) throw new Error('Gagal mengambil data');
        const data = await res.json();
        document.getElementById('nama').value = data.nama || '';
        document.getElementById('fakultas').value = data.fakultas || '';
        document.getElementById('prodi').value = data.prodi || '';
        document.getElementById('prodi_kode').value = data.prodi_kode || '';
        document.getElementById('jenjang').value = data.jenjang || '';
        document.getElementById('tempat_lahir').value = data.tempat_lahir || '';
        document.getElementById('email').value = data.email || '';
        document.getElementById('no_hp').value = data.no_hp || '';
    } catch (e) {
        alert('Data tidak ditemukan / error API');
    }
});
</script>
@endsection


