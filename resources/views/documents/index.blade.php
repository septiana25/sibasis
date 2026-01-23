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

        <!-- Search and Filters -->
        <div class="card mb-3">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.documents.index') }}" id="search-form">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="search" class="form-label">Cari</label>
                            <input type="text" name="search" id="search" class="form-control"
                                placeholder="NIM, Nama, Judul..." value="{{ request('search') }}">
                        </div>

                        <div class="col-md-2">
                            <label for="status" class="form-label">Status</label>
                            <select name="status" id="status" class="form-select">
                                <option value="">Semua Status</option>
                                @foreach ($filterOptions['statuses'] as $key => $value)
                                    <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>
                                        {{ $value }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label for="tahun" class="form-label">Tahun</label>
                            <select name="tahun" id="tahun" class="form-select">
                                <option value="">Semua Tahun</option>
                                @foreach ($filterOptions['years'] as $year)
                                    <option value="{{ $year }}" {{ request('tahun') == $year ? 'selected' : '' }}>
                                        {{ $year }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label for="fakultas" class="form-label">Fakultas</label>
                            <select name="fakultas" id="fakultas" class="form-select">
                                <option value="">Semua Fakultas</option>
                                @foreach ($filterOptions['fakultas'] as $fak)
                                    <option value="{{ $fak['id'] }}"
                                        {{ request('fakultas') == $fak['id'] ? 'selected' : '' }}>
                                        {{ $fak['nama_fakultas'] }}
                                    </option>
                                @endforeach
                            </select>
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

                    <div class="row g-3 mt-2" id="advanced-filters" style="display: none;">
                        <div class="col-md-3">
                            <label for="date_from" class="form-label">Tanggal Dari</label>
                            <input type="date" name="date_from" id="date_from" class="form-control"
                                value="{{ request('date_from') }}">
                        </div>

                        <div class="col-md-3">
                            <label for="date_to" class="form-label">Tanggal Sampai</label>
                            <input type="date" name="date_to" id="date_to" class="form-control"
                                value="{{ request('date_to') }}">
                        </div>
                    </div>

                    <div class="d-flex gap-2 mt-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-search"></i> Cari
                        </button>
                        <a href="{{ route('admin.documents.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-clockwise"></i> Reset
                        </a>
                        <button type="button" class="btn btn-outline-info" onclick="toggleAdvancedSearch()">
                            <i class="bi bi-funnel"></i> Filter Lanjut
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Results Info -->
        <div class="d-flex justify-content-between align-items-center mb-2">
            <div class="text-muted">
                Menampilkan {{ $documents->firstItem() ?? 0 }} - {{ $documents->lastItem() ?? 0 }}
                dari {{ $documents->total() }} hasil
                @if (request()->hasAny(['search', 'status', 'tahun', 'fakultas', 'date_from', 'date_to']))
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
                                <th>Judul</th>
                                <th>Fakultas</th>
                                <th>Prodi</th>
                                <th>Tahun</th>
                                <th>Status</th>
                                <th width="120">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $no = $documents->firstItem() ?? 1;
                            @endphp
                            @forelse ($documents as $doc)
                                <tr data-id="{{ $doc->id }}">
                                    <td>
                                        <span class="badge bg-secondary">{{ $no }}</span>
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $doc->nim }}</small>
                                    </td>
                                    <td>
                                        <div class="text-truncate" style="max-width: 200px;" title="{{ $doc->nama }}">
                                            <small class="text-muted">{{ $doc->nama }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $doc->judul }}</small>
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $doc->fakultasRelation->nama_fakultas }}</small>
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $doc->prodiRelation->nama_prodi }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $doc->tahun }}</span>
                                    </td>
                                    <td>
                                        @php
                                            $statusColors = [
                                                'pending' => 'warning',
                                                'approved' => 'success',
                                                'rejected' => 'danger',
                                                'padding' => 'secondary',
                                            ];
                                            $color = $statusColors[$doc->status] ?? 'secondary';
                                        @endphp
                                        <span class="badge bg-{{ $color }}">{{ ucfirst($doc->status) }}</span>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ route('admin.documents.show', $doc) }}" target="_blank"
                                                class="btn btn-outline-primary" title="Lihat PDF">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <button type="button" class="btn btn-outline-info"
                                                onclick="showDocumentDetail({{ $doc->id }})" title="Detail">
                                                <i class="bi bi-info-circle"></i>
                                            </button>
                                        </div>
                                    </td>
                                    @php
                                        $no++;
                                    @endphp
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-5">
                                        <div class="text-muted">
                                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                            @if (request()->hasAny(['search', 'status', 'tahun', 'fakultas']))
                                                Tidak ada dokumen yang sesuai dengan filter.
                                                <br>
                                                <a href="{{ route('admin.documents.index') }}"
                                                    class="btn btn-sm btn-outline-primary mt-2">
                                                    Reset Filter
                                                </a>
                                            @else
                                                Belum ada dokumen yang diupload.
                                                <br>
                                                <a href="{{ route('admin.documents.create') }}"
                                                    class="btn btn-sm btn-primary mt-2">
                                                    Upload Dokumen Pertama
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    </table>
                </div>
            </div>
            @if ($documents->hasPages())
                <div class="card-footer">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted">
                            Halaman {{ $documents->currentPage() }} dari {{ $documents->lastPage() }}
                        </div>
                        <div>
                            {{ $documents->onEachSide(2)->links() }}
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

        // Toggle advanced search
        function toggleAdvancedSearch() {
            const advancedFilters = document.getElementById('advanced-filters');
            const isHidden = advancedFilters.style.display === 'none';

            advancedFilters.style.display = isHidden ? 'flex' : 'none';

            // Update button text
            const btn = event.target.closest('button');
            const icon = btn.querySelector('i');
            if (isHidden) {
                btn.innerHTML = '<i class="bi bi-funnel-fill"></i> Sembunyikan Filter';
                icon.classList.replace('bi-funnel', 'bi-funnel-fill');
            } else {
                btn.innerHTML = '<i class="bi bi-funnel"></i> Filter Lanjut';
                icon.classList.replace('bi-funnel-fill', 'bi-funnel');
            }
        }

        // Document actions
        function showDocumentDetail(id) {
            // For now, just show an alert. In real implementation, this would open a modal
            alert('Fitur detail dokumen akan segera tersedia. ID: ' + id);
        }

        // Show advanced filters if they have values
        document.addEventListener('DOMContentLoaded', function() {
            const dateFrom = document.getElementById('date_from').value;
            const dateTo = document.getElementById('date_to').value;

            if (dateFrom || dateTo) {
                document.getElementById('advanced-filters').style.display = 'flex';
                const btn = document.querySelector('button[onclick="toggleAdvancedSearch()"]');
                btn.innerHTML = '<i class="bi bi-funnel-fill"></i> Sembunyikan Filter';
            }
        });
    </script>
@endsection
