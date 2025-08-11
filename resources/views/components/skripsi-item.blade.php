<div class="card mb-3">
    <div class="card-body p-3">
        <div class="d-flex justify-content-between align-items-start flex-wrap">
            <!-- Kiri: Informasi Skripsi -->
            <div class="me-3" style="flex: 1 1 auto;">
                <h5 class="mb-2 fw-semibold">{{ $skripsi['judul_ta'] }}</h5>
                <p class="mb-1 text-muted small">
                    {{ $skripsi['nim'] }} - {{ $skripsi['nama'] }} 
                    <span class="badge bg-secondary">{{ $skripsi['fakultas'] }}</span>
                    <span class="badge bg-light text-dark">{{ $skripsi['program_studi'] }}</span>
                </p>
                
                <!-- Dropdown Fakultas & Jurusan -->
                <div class="row g-2">
                    <div class="col-md-6">
                        <select class="form-select form-select-sm" name="fakultas">
                            <option value="">Pilih Fakultas</option>
                            @foreach ($fakultasList as $fak)
                            <option value="{{ $fak['id'] }}" @selected($fak['id'] == ($selectedFakultas ?? ''))>{{ $fak['nama'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 position-relative">
                        <select class="form-select form-select-sm" name="prodi">
                            <option value="">Pilih Jurusan</option>
                            @foreach ($prodiList as $prodi)
                                <option value="{{ $prodi['id'] }}" @selected($prodi['id'] == ($selectedProdi ?? ''))>{{ $prodi['nama'] }}</option>
                            @endforeach
                        </select>
                       {{--  @dd($fakultasList) --}}
                        <!-- Tooltip -->
                        <span class="position-absolute top-0 end-0 me-2 mt-1" data-bs-toggle="tooltip" data-bs-placement="left" title="Jika ingin pencarian berdasarkan jurusan & fakultas, boleh kosong">
                            <i class="bi bi-info-circle text-secondary"></i>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Kanan: Tombol -->
            <div class="text-end">
                <form action="{{ route('skripsi.show', $index) }}" method="get">
                    <button class="btn btn-outline-primary btn-sm text-nowrap">
                        <i class="bi bi-eye"></i> Lihat Data
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
