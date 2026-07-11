@extends('layouts.app')

@section('title', 'Daftar Dokumen Skripsi')

@section('content')

<div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3>Laporan Pengunjung</h3>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <!-- Search and Filters -->
        <div class="card mb-3">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.reports.visitor') }}" id="search-form">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="search" class="form-label">Cari</label>
                            <input type="text" name="search" id="search" class="form-control"
                                placeholder="Member, Nama" value="{{ request('search') }}">
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
                                <option value="15" {{ request('per_page', '15') == '15' ? 'selected' : '' }}>15
                                </option>
                                <option value="25" {{ request('per_page') == '25' ? 'selected' : '' }}>25</option>
                                <option value="50" {{ request('per_page') == '50' ? 'selected' : '' }}>50</option>
                            </select>
                        </div>
                    </div>


                    <div class="d-flex gap-2 mt-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-search"></i> Cari
                        </button>
                        <a href="{{ route('admin.reports.visitor') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-clockwise"></i> Reset
                        </a>
                        <a href="{{ route('admin.reports.visitor.export', array_filter([
                                'search'    => request('search'),
                                'date_from' => request('date_from'),
                                'date_to'   => request('date_to'),
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
                Total {{ $visitors->total() }} Kunjungan dari tanggal {{ request('date_from', now()->startOfMonth()->toDateString()) }} sampai
                {{ request('date_to', now()->endOfMonth()->toDateString()) }}

                @if (request()->hasAny(['search', 'date_from', 'date_to']))
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
                                <th>#</th>
                                <th>NIM</th>
                                <th>Nama</th>
                                <th>Type Anggota</th>
                                <th>Tanggal Kunjungan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $no = $visitors->firstItem() ?? 1;
                            @endphp
                            @forelse ($visitors as $visitor)
                                <tr>
                                    <td>{{ $no }}</td>
                                    <td>{{ $visitor->member_id }}</td>
                                    <td>{{ $visitor->member_name }}</td>
                                    <td>{{ $visitor->memberType->member_type_name ?? 'N/A' }}</td>
                                    <td>{{ $visitor->last_visit ? \Carbon\Carbon::parse($visitor->last_visit)->format('d-m-Y') : 'N/A' }}</td>
                                </tr>
                                @php
                                    $no++;
                                @endphp
                                @empty
                                <tr>
                                    <td colspan="9" class="text-center py-5">
                                        <div class="text-muted">
                                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                            @if (request()->hasAny(['search', 'date_from', 'date_to']))
                                                Tidak ada laporan yang sesuai dengan filter.
                                                <br>
                                                <a href="{{ route('admin.reports.visitor') }}"
                                                    class="btn btn-sm btn-outline-primary mt-2">
                                                    Reset Filter
                                                </a>
                                            @else
                                                Belum ada laporan yang tersedia.
                                                <br>
                                                <a href="{{ route('admin.reports.visitor') }}"
                                                    class="btn btn-sm btn-primary mt-2">
                                                    Buat Laporan Pertama
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if ($visitors->hasPages())
                <div class="card-footer">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted">
                            Halaman {{ $visitors->currentPage() }} dari {{ $visitors->lastPage() }}
                        </div>
                        <div>
                            {{ $visitors->onEachSide(2)->links('pagination.bootstrap-5') }}
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- JavaScript for enhanced functionality -->
    <script>
        // Auto-submit form on filter change
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('search-form');
            const selects = form.querySelectorAll('select');

            

            selects.forEach(select => {
                select.addEventListener('change', function() {
                    if (this.name !== 'per_page') {
                        form.submit();
                    }
                });
            });

            // Per page change
            document.getElementById('per_page').addEventListener('change', function() {
                form.submit();
            });

            // Search on Enter
            document.getElementById('search').addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    form.submit();
                }
            });

            // Debounced search
            let searchTimeout;
            document.getElementById('search').addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    if (this.value.length === 0 || this.value.length >= 3) {
                        form.submit();
                    }
                }, 500);
            });
        });


        // Helper functions
        function clearValidationErrors() {
            document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
            document.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');
        }

        function showValidationErrors(errors) {
            Object.keys(errors).forEach(field => {
                const input = document.getElementById(`edit_${field}`);
                const feedback = input.nextElementSibling;

                if (input) {
                    input.classList.add('is-invalid');
                    if (feedback && feedback.classList.contains('invalid-feedback')) {
                        feedback.textContent = errors[field][0];
                    }
                }
            });
        }


        function showAlert(type, message) {
            const alertHtml = `
                <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;

            const container = document.querySelector('.container');
            const firstChild = container.firstElementChild;
            const alertDiv = document.createElement('div');
            alertDiv.innerHTML = alertHtml;
            container.insertBefore(alertDiv.firstElementChild, firstChild.nextSibling);
        }
    </script>

    <!-- Custom Pagination Styles -->
    <style>
        .pagination .page-link {
            padding: 0.375rem 0.75rem;
            font-size: 0.875rem;
            border-radius: 0.25rem;
            border-color: #dee2e6;
            color: #6c757d;
        }

        .pagination .page-item:not(:first-child) .page-link {
            margin-left: 0;
        }

        .pagination .page-link:hover {
            background-color: #e9ecef;
            border-color: #adb5bd;
            color: #0d6efd;
        }

        .pagination .page-item.active .page-link {
            background-color: #0d6efd;
            border-color: #0d6efd;
            color: #fff;
        }

        .pagination .page-item.disabled .page-link {
            background-color: #fff;
            border-color: #dee2e6;
            color: #6c757d;
            pointer-events: none;
        }

        .pagination .page-link i {
            font-size: 0.75rem;
        }

        /* Responsive pagination */
        @media (max-width: 576px) {
            .pagination .page-link {
                padding: 0.25rem 0.5rem;
                font-size: 0.75rem;
            }
        }
    </style>
@endsection