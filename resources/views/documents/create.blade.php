@extends('layouts.app')

@section('title', 'Upload Dokumen PDF')

@section('content')
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3>Upload Dokumen PDF</h3>
            <a href="{{ route('admin.documents.index') }}" class="btn btn-outline-primary">Kembali</a>
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

        <div class="card mb-4 bg-secondary bg-opacity-10">
            <div class="card-body">
                <form action="{{ route('admin.documents.store') }}" method="POST" enctype="multipart/form-data"
                    id="upload-form">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="nim" class="form-label">NIM</label>
                            <div class="input-group">
                                <input type="text" name="nim" id="nim" class="form-control"
                                    value="{{ old('nim') }}" required>
                                <button class="btn btn-outline-secondary" type="button" id="btn-cek">Cek</button>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label for="nama" class="form-label">Nama</label>
                            <input type="text" name="nama" id="nama" class="form-control"
                                value="{{ old('nama') }}" readonly required>
                        </div>

                        <div class="col-md-4">
                            <label for="no_hp" class="form-label">No Hp</label>
                            <input type="text" name="no_hp" id="no_hp" class="form-control"
                                value="{{ old('no_hp') }}" readonly>
                        </div>

                        <div class="col-md-4">
                            <label for="email" class="form-label">Email</label>
                            <input type="text" name="email" id="email" class="form-control"
                                value="{{ old('email') }}" readonly>
                        </div>

                        <div class="col-md-8">
                            <label for="alamat" class="form-label">Alamat</label>
                            <input type="text" name="alamat" id="alamat" class="form-control"
                                value="{{ old('alamat') }}" readonly>
                        </div>

                        <div class="col-md-10">
                            <label for="judul" class="form-label">Judul Skripsi <span
                                    class="text-danger">*</span></label>
                            <textarea name="judul" id="judul" cols="30" rows="1" class="form-control" required>{{ old('judul') }}</textarea>

                        </div>
                        <div class="col-md-2">
                            <label for="tahun" class="form-label">Tahun Skripsi <span
                                    class="text-danger">*</span></label>
                            <input type="text" name="tahun" id="tahun" class="form-control"
                                value="{{ old('tahun') }}" required autocomplete="on">
                        </div>

                        <div class="col-md-6">
                            <label for="fakultas" class="form-label">Fakultas</label>
                            <input type="text" name="fakultas" id="fakultas" class="form-control"
                                value="{{ old('fakultas') }}" readonly required>
                        </div>

                        <div class="col-md-6">
                            <label for="prodi" class="form-label">Prodi</label>
                            <input type="text" name="prodi" id="prodi" class="form-control"
                                value="{{ old('prodi') }}" readonly required>
                            <input type="hidden" name="prodi_kode" id="prodi_kode" value="{{ old('prodi_kode') }}">
                        </div>

                        <div class="col-12">
                            <label for="file" class="form-label">File PDF (max 10MB) <span
                                    class="text-danger">*</span></label>
                            <input type="file" name="file" id="file" accept="application/pdf"
                                class="form-control" required>
                        </div>
                    </div>
                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary" id="btn-submit">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Cache DOM elements and configuration

        const spinnerHTML = (message) => `
            <span class="spinner-border spinner-border-sm" aria-hidden="true"></span>
            <span role="status">${message}</span>
        `;

        const studentLookup = {
            // Cache DOM elements for better performance
            elements: {
                btnCek: document.getElementById('btn-cek'),
                btnSubmit: document.getElementById('btn-submit'),
                nim: document.getElementById('nim'),
                nama_lengkap: document.getElementById('nama'),
                fakultas: document.getElementById('fakultas'),
                program_studi: document.getElementById('prodi'),
                program_studi_kode: document.getElementById('prodi_kode'),
                alamat: document.getElementById('alamat'),
                email: document.getElementById('email'),
                nomor_handphone: document.getElementById('no_hp') // Fixed: should be 'no_hp' not 'hp'
            },

            // Configuration
            config: {
                url: `{{ route('admin.documents.lookup') }}`,
                token: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },

            // Field names mapping for cleaner code
            fieldNames: ['nama_lengkap', 'fakultas', 'program_studi', 'program_studi_kode', 'alamat', 'email',
                'nomor_handphone'
            ],

            // Set loading state
            setLoadingState(isLoading) {
                this.elements.btnCek.disabled = isLoading;
                this.elements.btnCek.innerHTML = isLoading ?
                    spinnerHTML('Mengecek...') :
                    'Cek';
                this.elements.btnSubmit.disabled = isLoading;
                this.elements.btnSubmit.innerHTML = isLoading ?
                    spinnerHTML('Mengecek...') :
                    'Simpan';
                this.elements.nim.disabled = isLoading;
            },

            // Clear or populate form fields
            updateFields(data = {}) {
                this.fieldNames.forEach(field => {
                    const element = this.elements[field];
                    if (element) {
                        element.value = data[field] || '';
                    }
                });
            },

            // Show user-friendly messages
            showMessage(message, isError = false) {
                // You can replace this with a more sophisticated notification system
                if (isError) {
                    alert(`Error: ${message}`);
                } else {
                    // Success message could be shown here if needed
                }
            },

            // Validate input
            validateNim(nim) {
                if (!nim || nim.length < 3) {
                    this.showMessage('NIM minimal 3 karakter', true);
                    return false;
                }
                return true;
            },

            // Main lookup function
            async lookupStudent() {
                const nim = this.elements.nim.value.trim();

                if (!this.validateNim(nim)) return;

                this.setLoadingState(true);

                try {
                    const response = await fetch(this.config.url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': this.config.token,
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({
                            nim
                        })
                    });


                    if (response.ok) {
                        const data = await response.json();
                        this.updateFields(data[0] || {}); // Assuming data is an array

                        // Check if data was actually found
                        if (!data || data.length === 0) {
                            this.showMessage('Data mahasiswa tidak ditemukan', true);
                        }
                    } else {
                        // Handle HTTP errors
                        const errorText = await response.text();
                        this.updateFields(); // Clear fields
                        this.showMessage(`Server error: ${response.status} - ${errorText}`, true);
                    }
                } catch (error) {
                    // Handle network errors
                    this.updateFields(); // Clear fields
                    this.showMessage(`Network error: ${error.message}`, true);
                } finally {
                    this.setLoadingState(false);
                }
            },

            // Initialize event listeners
            init() {
                this.elements.btnCek.addEventListener('click', () => this.lookupStudent());

                // Allow Enter key on NIM input
                this.elements.nim.addEventListener('keypress', (e) => {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        this.lookupStudent();
                    }
                });

                let searchTimeout;
                this.elements.nim.addEventListener('input', (e) => {
                    this.updateFields(); // Clear fields on NIM input change
                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(() => {
                        if (e.target.value.length === 10) {
                            e.preventDefault();
                            this.lookupStudent();
                        }
                    }, 500);
                });
            }
        };

        // Initialize when DOM is ready
        document.addEventListener('DOMContentLoaded', () => {
            studentLookup.init();

            const form = document.getElementById('upload-form');
            const submitButton = document.getElementById('btn-submit');

            form.addEventListener('submit', function() {
                submitButton.disabled = true;
                submitButton.innerHTML = spinnerHTML('Menyimpan...');
            });
        });
    </script>
@endsection
