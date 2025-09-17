@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Daftar Dokumen</h3>
        <div>
            <a href="{{ route('admin.documents.export') }}" class="btn btn-outline-secondary">Export CSV</a>
            <a href="{{ route('admin.documents.create') }}" class="btn btn-primary">Upload Dokumen</a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>NIM</th>
                            <th>Nama</th>
                            <th>Fakultas</th>
                            <th>Prodi</th>
                            <th>Tahun</th>
                            <th>Status</th>
                            <th>File</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($documents as $doc)
                            <tr>
                                <td>{{ $doc->id }}</td>
                                <td>{{ $doc->nim }}</td>
                                <td>{{ $doc->nama }}</td>
                                <td>{{ $doc->fakultas }}</td>
                                <td>{{ $doc->prodi }}</td>
                                <td>{{ $doc->tahun }}</td>
                                <td>{{ $doc->status }}</td>
                                <td>
                                    <a href="{{ route('admin.documents.show', $doc) }}" target="_blank" class="btn btn-sm btn-outline-primary">Lihat</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">Belum ada data</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            {{ $documents->links() }}
        </div>
    </div>
</div>
@endsection


