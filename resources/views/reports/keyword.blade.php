@extends('layouts.app')

@section('title', 'Tren Kata Kunci Pencarian')

@section('content')

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Tren Kata Kunci Pencarian Tugas Akhir</h3>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <!-- Search and Filters -->
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.reports.keyword') }}" id="search-form">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="search" class="form-label">Cari</label>
                        <input type="text" name="search" id="search" class="form-control"
                            placeholder="Judul TA, NIM, atau Nama" value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <label for="date_from" class="form-label">Dari Tanggal</label>
                        <input type="date" name="date_from" id="date_from" class="form-control"
                            value="{{ request('date_from', now()->startOfMonth()->toDateString()) }}">
                    </div>
                    <div class="col-md-2">
                        <label for="date_to" class="form-label">Sampai Tanggal</label>
                        <input type="date" name="date_to" id="date_to" class="form-control"
                            value="{{ request('date_to', now()->endOfMonth()->toDateString()) }}">
                    </div>
                    <div class="col-md-2">
                        <label for="per_page" class="form-label">Per Halaman</label>
                        <select name="per_page" id="per_page" class="form-select">
                            <option value="10" {{ request('per_page') == '10' ? 'selected' : '' }}>10</option>
                            <option value="15" {{ request('per_page', '15') == '15' ? 'selected' : '' }}>15</option>
                            <option value="25" {{ request('per_page') == '25' ? 'selected' : '' }}>25</option>
                            <option value="50" {{ request('per_page') == '50' ? 'selected' : '' }}>50</option>
                        </select>
                    </div>
                </div>

                <div class="row g-3 mt-0">
                    <div class="col-md-3">
                        <label for="fakultas" class="form-label">Fakultas</label>
                        <input type="text" name="fakultas" id="fakultas" class="form-control"
                            placeholder="Contoh: FITK" value="{{ request('fakultas') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="program_studi" class="form-label">Program Studi</label>
                        <input type="text" name="program_studi" id="program_studi" class="form-control"
                            placeholder="Contoh: Pendidikan Agama Islam" value="{{ request('program_studi') }}">
                    </div>
                </div>

                <div class="d-flex gap-2 mt-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search"></i> Cari
                    </button>
                    <a href="{{ route('admin.reports.keyword') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-clockwise"></i> Reset
                    </a>
                    <a href="{{ route('admin.reports.keyword.export', array_filter([
                            'search'        => request('search'),
                            'date_from'     => request('date_from'),
                            'date_to'       => request('date_to'),
                            'fakultas'      => request('fakultas'),
                            'program_studi' => request('program_studi'),
                        ])) }}" class="btn btn-success ms-auto">
                        <i class="bi bi-file-earmark-spreadsheet"></i> Export Excel
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Results Info -->
    <div class="d-flex justify-content-between align-items-center mb-2">
        <div class="text-muted">
            Total <strong>{{ $keywords->total() }}</strong> judul tugas akhir dari
            {{ request('date_from', now()->startOfMonth()->toDateString()) }} sampai
            {{ request('date_to', now()->endOfMonth()->toDateString()) }}
            @if (request()->hasAny(['search', 'date_from', 'date_to', 'fakultas', 'program_studi']))
                <span class="badge bg-info ms-1">Filtered</span>
            @endif
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th style="width:50px">#</th>
                            <th>Judul Tugas Akhir</th>
                            <th>NIM</th>
                            <th>Nama</th>
                            <th>Program Studi</th>
                            <th>Fakultas</th>
                            <th class="text-center">Total Pencarian</th>
                            <th>Terakhir Dicari</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $no = $keywords->firstItem() ?? 1; @endphp
                        @forelse ($keywords as $item)
                            <tr>
                                <td>{{ $no }}</td>
                                <td>
                                    <span class="d-inline-block text-truncate" style="max-width:360px"
                                          title="{{ $item->judul_ta }}">
                                        {{ $item->judul_ta }}
                                    </span>
                                </td>
                                <td>{{ $item->nim }}</td>
                                <td>{{ $item->nama }}</td>
                                <td>{{ $item->program_studi }}</td>
                                <td>{{ $item->fakultas }}</td>
                                <td class="text-center">
                                    <span class="badge bg-primary rounded-pill">{{ $item->total_searches }}</span>
                                </td>
                                <td>
                                    {{ $item->last_searched
                                        ? \Carbon\Carbon::parse($item->last_searched)->format('d-m-Y')
                                        : 'N/A' }}
                                </td>
                            </tr>
                            @php $no++; @endphp
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                        @if (request()->hasAny(['search', 'date_from', 'date_to', 'fakultas', 'program_studi']))
                                            Tidak ada data yang sesuai dengan filter.
                                            <br>
                                            <a href="{{ route('admin.reports.keyword') }}"
                                                class="btn btn-sm btn-outline-primary mt-2">Reset Filter</a>
                                        @else
                                            Belum ada data pencarian tersedia.
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($keywords->hasPages())
            <div class="card-footer">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted">
                        Halaman {{ $keywords->currentPage() }} dari {{ $keywords->lastPage() }}
                    </div>
                    <div>
                        {{ $keywords->onEachSide(2)->links('pagination.bootstrap-5') }}
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('search-form');

        document.getElementById('per_page').addEventListener('change', function () {
            form.submit();
        });

        document.getElementById('search').addEventListener('keypress', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                form.submit();
            }
        });

        let searchTimeout;
        document.getElementById('search').addEventListener('input', function () {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                if (this.value.length === 0 || this.value.length >= 3) {
                    form.submit();
                }
            }, 500);
        });
    });
</script>

<style>
    .pagination .page-link {
        padding: 0.375rem 0.75rem;
        font-size: 0.875rem;
        border-radius: 0.25rem;
        border-color: #dee2e6;
        color: #6c757d;
    }
    .pagination .page-item:not(:first-child) .page-link { margin-left: 0; }
    .pagination .page-link:hover { background-color: #e9ecef; border-color: #adb5bd; color: #0d6efd; }
    .pagination .page-item.active .page-link { background-color: #0d6efd; border-color: #0d6efd; color: #fff; }
    .pagination .page-item.disabled .page-link { background-color: #fff; border-color: #dee2e6; color: #6c757d; pointer-events: none; }
    @media (max-width: 576px) {
        .pagination .page-link { padding: 0.25rem 0.5rem; font-size: 0.75rem; }
    }
</style>

@endsection
