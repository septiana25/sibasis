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

    <div class="card mb-4 bg-secondary bg-opacity-10">
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

                    <div class="col-md-6">
                        <label for="email" class="form-label">Email</label>
                        <input type="text" name="email" id="email" class="form-control" value="{{ old('email') }}" readonly required>
                    </div>

                    <div class="col-md-6">
                        <label for="tempat_lahir" class="form-label">Tempat Lahir</label>
                        <input type="text" name="tempat_lahir" id="tempat_lahir" class="form-control" value="{{ old('tempat_lahir') }}" readonly required>
                    </div>
                    
                    <div class="col-md-10">
                        <label for="judul" class="form-label">Judul Skripsi</label>
                        <textarea name="judul" id="judul" cols="30" rows="3" class="form-control" required>{{ old('judul') }}</textarea>
                        
                    </div>
                    <div class="col-md-2">
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
                    <input type="hidden" name="prodi_kode" id="prodi_kode" value="{{ old('prodi_kode') }}">
                    <input type="hidden" name="jenjang" id="jenjang" value="{{ old('jenjang') }}">
                </div>
                <div class="mt-3">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Cache DOM elements and configuration
const studentLookup = {
    // Cache DOM elements for better performance
    elements: {
        btnCek: document.getElementById('btn-cek'),
        nim: document.getElementById('nim'),
        nama: document.getElementById('nama'),
        fakultas: document.getElementById('fakultas'),
        prodi: document.getElementById('prodi'),
        prodi_kode: document.getElementById('prodi_kode'),
        jenjang: document.getElementById('jenjang'),
        tempat_lahir: document.getElementById('tempat_lahir'),
        email: document.getElementById('email'),
        no_hp: document.getElementById('hp') // Fixed: should be 'hp' not 'no_hp'
    },
    
    // Configuration
    config: {
        url: `{{ route('admin.documents.lookup') }}`,
        token: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    },

    // Field names mapping for cleaner code
    fieldNames: ['nama', 'fakultas', 'prodi', 'prodi_kode', 'jenjang', 'tempat_lahir', 'email', 'no_hp'],

    // Set loading state
    setLoadingState(isLoading) {
        this.elements.btnCek.disabled = isLoading;
        this.elements.btnCek.textContent = isLoading ? 'Mengecek...' : 'Cek';
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
            console.log(message);
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
                body: JSON.stringify({ nim })
            });

            if (response.ok) {
                const data = await response.json();
                this.updateFields(data);
                
                // Check if data was actually found
                if (!data.nama) {
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
            console.error('Lookup error:', error);
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
    }
};

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    studentLookup.init();
});
</script>
@endsection


