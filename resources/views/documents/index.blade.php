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
                                <th width="150">Aksi</th>
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
                                            <button type="button" class="btn btn-outline-warning"
                                                onclick="editDocument({{ $doc->id }})" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <!-- Hidden trigger for modal -->
                                            <button type="button" class="d-none" data-edit-id="{{ $doc->id }}"
                                                data-bs-toggle="modal" data-bs-target="#editDocumentModal"></button>
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
                            {{ $documents->onEachSide(2)->links('pagination.bootstrap-5') }}
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Edit Document Modal -->
    <div class="modal fade" id="editDocumentModal" tabindex="-1" aria-labelledby="editDocumentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editDocumentModalLabel">Edit Dokumen</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editDocumentForm" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <input type="hidden" id="edit_document_id" name="document_id">
                        
                        <!-- Judul -->
                        <div class="mb-3">
                            <label for="edit_judul" class="form-label">Judul Skripsi <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="edit_judul" name="judul" rows="2" required 
                                placeholder="Masukkan judul skripsi..."></textarea>
                            <div class="invalid-feedback"></div>
                        </div>

                        <!-- Tahun -->
                            <input type="hidden" class="form-control" id="edit_tahun" name="tahun" 
                                placeholder="Masukkan tahun..." required min="1900" max="{{ date('Y') + 1 }}">

                        <!-- File PDF -->
                        <div class="mb-3">
                            <label for="edit_file" class="form-label">File PDF (Opsional)</label>
                            <input type="file" class="form-control" id="edit_file" name="file" 
                                accept=".pdf">
                            <div class="form-text">
                                Kosongkan jika tidak ingin mengubah file. File harus berformat PDF, maksimal 10MB.
                            </div>
                            <div class="invalid-feedback"></div>
                            
                            <!-- Current file info -->
                            <div id="current_file_info" class="mt-2" style="display: none;">
                                <small class="text-muted">
                                    <i class="bi bi-file-pdf"></i> File saat ini: <span id="current_file_name"></span>
                                </small>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">
                            <span class="spinner-border spinner-border-sm me-2" id="edit_loading" style="display: none;"></span>
                            Update
                        </button>
                    </div>
                </form>
            </div>
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

        // Edit document function
        function editDocument(id) {
            // Fetch document data
            fetch(`/admin/documents/${id}/edit`)
                .then(response => response.json())
                .then(data => {
                    // Populate modal fields
                    document.getElementById('edit_document_id').value = data.id;
                    document.getElementById('edit_judul').value = data.judul;
                    document.getElementById('edit_tahun').value = data.tahun;
                    
                    // Show current file info
                    document.getElementById('current_file_name').textContent = data.file_path.split('/').pop();
                    document.getElementById('current_file_info').style.display = 'block';
                    
                    // Clear any previous validation errors
                    clearValidationErrors();
                    
                    // Show modal using data attribute trigger
                    const modalTrigger = document.querySelector(`[data-edit-id="${id}"]`);
                    if (modalTrigger) {
                        modalTrigger.click();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Gagal memuat data dokumen');
                });
        }

        // Handle edit form submission
        document.getElementById('editDocumentForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const documentId = document.getElementById('edit_document_id').value;
            const loadingSpinner = document.getElementById('edit_loading');
            const submitButton = this.querySelector('button[type="submit"]');
            
            // Show loading state
            loadingSpinner.style.display = 'inline-block';
            submitButton.disabled = true;
            
            // Clear previous validation errors
            clearValidationErrors();
            
            fetch(`/admin/documents/${documentId}`, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update table row with new data
                    updateTableRow(data.document);
                    
                    // Close modal using data attribute
                    const closeButton = document.querySelector('#editDocumentModal .btn-close');
                    if (closeButton) {
                        closeButton.click();
                    }
                    
                    // Show success message
                    showAlert('success', data.message);
                } else {
                    // Show validation errors
                    if (data.errors) {
                        showValidationErrors(data.errors);
                    } else {
                        showAlert('danger', data.message || 'Terjadi kesalahan');
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('danger', 'Terjadi kesalahan jaringan');
            })
            .finally(() => {
                // Hide loading state
                loadingSpinner.style.display = 'none';
                submitButton.disabled = false;
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

        function updateTableRow(documentData) {
            const row = document.querySelector(`tr[data-id="${documentData.id}"]`);
            console.log(row)
            if (row) {
                // Update judul cell
                const judulCell = row.children[3];
                judulCell.querySelector('small').textContent = documentData.judul;
                
                // Update tahun cell
                const tahunCell = row.children[6];
                tahunCell.querySelector('.badge').textContent = documentData.tahun;
            }
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
