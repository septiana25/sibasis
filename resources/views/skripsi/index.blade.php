@extends('layouts.app')

@section('title', 'Pencarian Skripsi')

@section('content')
<!-- Elegant Hero Section -->
<div class="hero-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-12">
                <!-- Centered Hero Content -->
                <div class="hero-content text-center">
                    <div class="hero-icon mb-4">
                        <i class="bi bi-search-heart" id="heroSearchIcon"></i>
                    </div>
                    <h1 class="hero-title" id="heroTitle"><span id="wordCari">Cari</span> Skripsi <span class="d-none" id="heroTitleSpan"> Sedang Kami Cari......</span></h1>
                    <p class="hero-subtitle">Temukan ribuan skripsi dari berbagai fakultas dan jurusan</p>
                </div>
                
                <!-- Elegant Search Form -->
                <div class="search-container">
                    <form action="{{ route('skripsi.search') }}" method="post" class="search-form">
                        @csrf
                        
                        <!-- Main Search Bar - More Prominent -->
                        <div class="search-bar-wrapper mb-4">
                            <div class="search-input-group">
                                <div class="search-icon">
                                    <i class="bi bi-search"></i>
                                </div>
                                <input 
                                    type="text" 
                                    name="judul" 
                                    id="judul" 
                                    class="search-input" 
                                    value="{{ request('judul') }}" 
                                    placeholder="Masukkan judul skripsi yang ingin dicari..."
                                    autocomplete="off"
                                >
                                <button type="submit" class="search-button">
                                    <i class="bi bi-search me-2" id="searchIcon"></i>
                                    <span class="spinner-border spinner-border-sm me-2 d-none" role="status" aria-hidden="true" id="searchSpinner"></span>
                                    Cari
                                </button>
                            </div>
                        </div>

                        <!-- Refined Filter Section -->
                        <div class="filter-section">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <div class="filter-group">
                                        <label for="fakultas" class="filter-label">
                                            <i class="bi bi-building me-2"></i>Fakultas
                                        </label>
                                        <select name="fakultas" id="fakultas" class="filter-select">
                                            <option value="">Semua Fakultas</option>
                                            @foreach ($fakultasList as $key => $fak)
                                            <option value="{{ $fak['id'] }}" {{ request('fakultas') == $fak['id'] ? 'selected' : '' }}>
                                                {{ $fak['nama_fakultas'] }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="filter-group">
                                        <label for="prodi" class="filter-label">
                                            <i class="bi bi-mortarboard me-2"></i>Jurusan
                                        </label>
                                        <select name="prodi" id="prodi" class="filter-select">
                                            <option value="">Semua Jurusan</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="filter-group">
                                        <label for="year" class="filter-label">
                                            <i class="bi bi-calendar me-2"></i>Tahun
                                        </label>
                                        <select id="year" name="tahun" class="filter-select">
                                            <option value="">Semua Tahun</option>
                                            @for ($year = date('Y'); $year >= 2015; $year--)
                                                <option value="{{ $year }}">{{ $year }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Content Section -->
<div class="content-section">
    <div class="container">
        <div class="row">
            <!-- Main Content Area -->
            <div class="col-lg-8">
                @if (is_null($results))
                    <!-- Elegant Loading Skeleton -->
                    <div class="skeleton-container">
                        @for ($i = 0; $i < 3; $i++)
                            <div class="skeleton-card">
                                <div class="skeleton-content">
                                    <div class="skeleton-title"></div>
                                    <div class="skeleton-text"></div>
                                    <div class="skeleton-text-short"></div>
                                </div>
                            </div>
                        @endfor
                    </div>
                @endif

                @if ($results['data'] ?? false)
                    <!-- Results Header -->
                    <div class="results-header">
                        <div class="results-title">
                            <i class="bi bi-list-ul"></i>
                            <span>Hasil Pencarian</span>
                        </div>
                        <div class="results-summary">
                            Ditemukan <strong>{{ $results['total'] }}</strong> skripsi, kata kunci
                            <strong>{{ trim(($results['judul_ta'] ?? '') . ' ' . ($results['tahun'] ?? '')) ?: '' }}</strong>
                        </div>
                    </div>

                    <!-- Elegant Results Grid -->
                    <div class="results-grid">
                        @foreach ($results['data'] as $index => $item)
                            <div class="result-card">
                                <div class="card-header">
                                    <div class="card-icon">
                                        <i class="bi bi-file-earmark-text"></i>
                                    </div>
                                    <div class="card-title">
                                        <a href="{{ route('skripsi.show', $index) }}" class="title-link">
                                            {{ $item['judul_ta'] }}
                                        </a>
                                    </div>
                                </div>
                                
                                <div class="card-body">
                                    <div class="meta-grid">
                                        <div class="meta-item">
                                            <i class="bi bi-person-circle"></i>
                                            <span class="meta-label">NIM:</span>
                                            <span class="meta-value">{{ $item['nim'] }}</span>
                                        </div>
                                        <div class="meta-item">
                                            <i class="bi bi-person"></i>
                                            <span class="meta-label">Nama:</span>
                                            <span class="meta-value">{{ $item['nama'] }}</span>
                                        </div>
                                        <div class="meta-item">
                                            <i class="bi bi-building"></i>
                                            <span class="meta-label">Fakultas:</span>
                                            <span class="meta-value">{{ $item['fakultas'] }}</span>
                                        </div>
                                        <div class="meta-item">
                                            <i class="bi bi-mortarboard"></i>
                                            <span class="meta-label">Program Studi:</span>
                                            <span class="meta-value">{{ $item['program_studi'] }}</span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="card-footer">
                                    <a href="{{ route('skripsi.show', $index) }}" class="view-button">
                                        <i class="bi bi-eye me-2"></i>Lihat Detail
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    @if(isset($results['data']) && count($results['data']) > 0)
                        <div class="pagination-wrapper">
                            <x-post-pagination 
                                :results="$results"
                                route="skripsi.search"
                                :params="[
                                    'judul' => $results['judul_ta'] ?? '',
                                    'fakultas' => $results['fakultas'] ?? '',
                                    'prodi' => $results['prodi'] ?? '',
                                ]"
                            />
                        </div>
                    @endif
                @else   
                    <!-- Elegant Empty State -->
                    <div class="empty-state">
                        <div class="empty-icon">
                            <i class="bi bi-search"></i>
                        </div>
                        <h3 class="empty-title">Mulai Pencarian</h3>
                        <p class="empty-description">Gunakan form pencarian di atas untuk menemukan skripsi yang Anda butuhkan</p>
                        
                        <div class="search-tips">
                            <h6 class="tips-title">Tips Pencarian:</h6>
                            <div class="tips-grid">
                                <div class="tip-item">
                                    <i class="bi bi-lightbulb"></i>
                                    <span>Gunakan kata kunci spesifik</span>
                                </div>
                                <div class="tip-item">
                                    <i class="bi bi-lightbulb"></i>
                                    <span>Filter berdasarkan fakultas</span>
                                </div>
                                <div class="tip-item">
                                    <i class="bi bi-lightbulb"></i>
                                    <span>Pilih tahun yang relevan</span>
                                </div> 
                                <div class="tip-item">
                                    <i class="bi bi-lightbulb"></i>
                                    <span>Kosongkan semua filter untuk menampilkan semua skripsi</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Elegant Sidebar -->
            <div class="col-lg-4">
                @if ($results['data'] ?? false)
                    <!-- Search Summary Card -->
                    <div class="sidebar-card summary-card">
                        <div class="card-header">
                            <i class="bi bi-graph-up"></i>
                            <span>Ringkasan Pencarian</span>
                        </div>
                        <div class="card-body">
                            <div class="summary-item">
                                <span class="summary-label">Total Hasil:</span>
                                <span class="summary-value">{{ $results['total'] }}</span>
                            </div>
                            <div class="summary-item">
                                <span class="summary-label">Halaman Saat Ini:</span>
                                <span class="summary-value">{{ $results['current_page'] }}</span>
                            </div>
                        </div>
                    </div>
                @endif
                
                <!-- Popular Searches Card -->
                <div class="sidebar-card popular-card">
                    <div class="card-header">
                        <i class="bi bi-fire"></i>
                        <span>Pencarian Populer</span>
                    </div>
                    
                    <div class="card-body">
                        @if (empty($popularKeywords))
                            <!-- Loading Skeleton for Popular -->
                            @for ($i = 0; $i < 3; $i++)
                                <div class="popular-skeleton">
                                    <div class="skeleton-line"></div>
                                    <div class="skeleton-line-short"></div>
                                </div>
                            @endfor
                        @else
                            <!-- Popular Searches List -->
                            <div class="popular-list">
                                @foreach ($popularKeywords as $index => $item)
                                    <div class="popular-item">
                                        <form action="{{ route('skripsi.search') }}" method="POST" class="mb-0">
                                            @csrf
                                            <input type="hidden" name="judul" value="{{ $item['judul_ta'] }}">
                                            <button type="submit" class="popular-button">
                                                <div class="popular-rank">{{ $index + 1 }}</div>
                                                <div class="popular-content">
                                                    <div class="popular-title">
                                                        {{ Str::limit($item['judul_ta'], 50) }}
                                                    </div>
                                                    <div class="popular-count">
                                                        <i class="bi bi-search me-1"></i>{{ $item['total'] }} pencarian
                                                    </div>
                                                </div>
                                            </button>
                                        </form>
                                    </div>
                                    @if (!$loop->last)
                                        <div class="popular-divider"></div>
                                    @endif
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



<script>
    // Enhanced Fakultas-Prodi Relationship
    const fakultasData = @json($fakultasList);

    document.getElementById('fakultas').addEventListener('change', function () {
        let fakultasId = parseInt(this.value);
        let prodiDropdown = document.getElementById('prodi');

        // Reset dropdown jurusan
        prodiDropdown.innerHTML = '<option value="">Semua Jurusan</option>';

        if (!isNaN(fakultasId)) {
            // Cari fakultas yang dipilih
            let selectedFakultas = fakultasData.find(f => f.id === fakultasId);
            
            if (selectedFakultas && selectedFakultas.prodi.length > 0) {
                selectedFakultas.prodi.forEach(function (p) {
                    let option = document.createElement('option');
                    option.value = p.kode;
                    option.textContent = p.nama_prodi;
                    prodiDropdown.appendChild(option);
                });
            }
        }
    });

    // Animated Search Button
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('.search-form');
        const button = form?.querySelector('.search-button');
        const icon = document.getElementById('searchIcon');
        const spinner = document.getElementById('searchSpinner');
        const heroIcon = document.getElementById('heroSearchIcon');
        const wordCari = document.getElementById('wordCari');
        const heroTitleSpan = document.getElementById('heroTitleSpan');

        function startLoading() {
          if (!button) return;
          if (!button.hasAttribute('disabled')) {
            button.setAttribute('disabled', 'disabled');
            button.classList.add('disabled');
          }
          if (icon) icon.classList.add('d-none');
          if (spinner) spinner.classList.remove('d-none');

          // Swap hero title parts
          if (wordCari) wordCari.classList.add('d-none');
          if (heroTitleSpan) heroTitleSpan.classList.remove('d-none');

          // Subtle visual cue on the hero icon + add a small spinner next to it
          if (heroIcon) {
            // swap heart icon to repeat icon during loading
            heroIcon.classList.remove('bi-search-heart');
            heroIcon.classList.add('opacity-50');
            if (!document.getElementById('heroSpinner')) {
              const hs = document.createElement('span');
              hs.id = 'heroSpinner';
              hs.className = 'spinner-border spinner-border-lg align-middle ms-2';
              hs.setAttribute('role', 'status');
              hs.setAttribute('aria-hidden', 'true');
              heroIcon.parentNode.appendChild(hs);
            }
          }
        }

        if (form) {
          form.addEventListener('submit', function() {
            startLoading();
            // Let the normal form submission proceed
          });
        }

        // Restore state when navigating back (bfcache)
        window.addEventListener('pageshow', function(e) {
          if (e.persisted) {
            stopLoading();
          }
        });
    });

    // Enhanced Search Experience
    document.addEventListener('DOMContentLoaded', function() {
        const searchForm = document.querySelector('.search-form');
        const searchInput = document.getElementById('judul');
        
        // Auto-focus search input
        searchInput.focus();
        
        // Enhanced form submission with validation
        /* searchForm.addEventListener('submit', function(e) {
            const searchValue = searchInput.value.trim();
            if (!searchValue) {
                e.preventDefault();
                searchInput.focus();
                // Add visual feedback
                searchInput.classList.add('is-invalid');
                setTimeout(() => searchInput.classList.remove('is-invalid'), 2000);
            }
        }); */
        
        // Real-time search validation
        /* searchInput.addEventListener('input', function() {
            if (this.value.trim()) {
                this.classList.remove('is-invalid');
            }
        }); */
        
        // Smooth scroll to results
        if (window.location.search.includes('judul=')) {
            setTimeout(() => {
                document.querySelector('.content-section')?.scrollIntoView({ 
                    behavior: 'smooth' 
                });
            }, 100);
        }
    });
</script>
@endsection
