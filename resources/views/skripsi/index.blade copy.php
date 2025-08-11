@extends('layouts.app')

@section('title', 'Pencarian Skripsi')

@section('content')
<div class="container mt-4">
    <div class="row">
        <!-- Kolom kiri -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">

                    <div class="card-title">
                        <h4 class="text-success-emphasis"><strong><i class="bi bi-search"></i> Pencarian Skripsi</strong></h4>
                    </div>
                    <form action="{{ route('skripsi.search') }}" method="post">
                        @csrf
                        <div class="row g-2 align-items-end">
                            <div class="col-md-10">
                                <label for="judul" class="form-label">Cari Judul Skripsi</label>
                                <input type="text" name="judul" id="judul" class="form-control form-control-lg" value="{{ request('judul') }}" placeholder="Contoh: Teknologi Pertanian">
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100 mt-md-4 btn-lg"><i class="bi bi-search"></i> <span class="d-md-none d-lg-inline">Cari</span></button>
                            </div>

                            <div class="col-md-5">
                                <label for="fakultas" class="form-label">Fakultas</label>
                                <select name="fakultas" id="fakultas" class="form-select">
                                    <option value="">-- Pilih Fakultas --</option>
                                    @foreach ($fakultasList as $key => $fak)
                                    <option value="{{ $fak['id'] }}" {{ request('fakultas') == $fak['id'] ? 'selected' : '' }}>
                                        {{ $fak['nama'] }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>                         
                            <div class="col-md-5">
                                <label for="prodi" class="form-label">Jurusan</label>
                                <select name="prodi" id="prodi" class="form-select">
                                    <option value="">-- Pilih Jurusan --</option>
                                    @foreach ($prodiList as $key => $prodi)
                                    <option value="{{ $prodi['id'] }}" {{ request('prodi') == $prodi['id'] ? 'selected' : '' }}>
                                        {{ $prodi['nama'] }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2 text-end">
                                <a data-bs-toggle="tooltip" title="Filter pencarian. Jika ingin mencari semua isian dikosongkan">
                                    <i class="bi bi-info-circle fs-5 text-danger mt-4"> Tips</i>
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            @if ($results['data'] ?? false)
                @foreach ($results['data'] as $index => $item)
                    <div class="card my-3">
                        <div class="card-body">
                            <div class="card-title">
                                <h5><strong>
                                    <a href="{{ route('skripsi.show', $index) }}" class="text-dark-emphasis text-uppercase link-offset-2 link-underline link-underline-opacity-0 text-reset"> 
                                        {{ $item['judul_ta'] }} 
                                    </a>
                                </strong></h5>
                            </div>
                            <div>
                                <span class="card-text me-3"><i class="bi bi-person"></i> {{ $item['nim'] }}</span>
                                <span class="card-text"><i class="bi bi-person"></i> {{ $item['nama'] }}</span>
                            </div>
                            <div>
                                <span class="card-text me-3"><i class="bi bi-building"></i> {{ $item['fakultas'] }}</span>
                                <span class="card-text me-3"><i class="bi bi-mortarboard"></i> {{ $item['program_studi'] }}</span>
                            </div>                  
                            <div class="d-flex justify-content-between">
                                <div class="d-flex flex-column">
                                </div>
                                <div class="">
                                    
                                </div>
                            </div>
                        </div>
                    </div>

                    @endforeach

                    <x-post-pagination 
                        :results="$results"
                        route="skripsi.search"
                        :params="[
                            'judul' => $results['judul_ta'] ?? '',
                            'fakultas' => $results['fakultas'] ?? '',
                            'prodi' => $results['prodi'] ?? '',
                        ]"
                    />
          
            @else   
                <div class="card text-center my-3">
                    <div class="card-body">
                        <h1>
                            <i class="bi bi-mortarboard"></i>
                        </h1>
                        <div class="card-title"><strong>Mulai pencarian untuk melihat hasil skripsi</strong></div>
                        <p>Gunakan form pencarian di atas untuk mencari skripsi</p>
                    </div>
                </div>
            @endif
        </div>

        <!-- Kolom kanan -->
        <div class="col-md-4">
            @if ($results['data'] ?? false)
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="card-title">
                            <h4 class="mb-3 text-success-emphasis"><strong><i class="bi bi-list-columns-reverse"></i> Hasil Pencarian</strong></h4>
                        </div>
                        <p class="card-text">Ditemukan <strong>{{$results['total']}}</strong> data pencarian. Anda berada pada halaman <strong>{{$results['current_page']}}</strong></p>
                    </div>
                </div>
            @endif
            
            <div class="card ">
                <div class="card-body">
                    <div class="card-title">
                    <h4 class="mb-3 text-success-emphasis"><strong><i class="bi bi-bar-chart"></i> Pencarian Populer</strong></h4>
                    </div>
                @foreach ($popularKeywords as $index => $item)
                    <div class="card mb-2">
                        <div class="card-body d-flex justify-content-between">
                            <a href="{{ route('skripsi.search', ['q' => $item['keyword']]) }}" class="card-title link-offset-2 link-underline link-underline-opacity-0">
                                <strong>{{ $index + 1 }}. {{ $item['keyword'] }}</strong><br>
                                <small>{{ $item['count'] }} pencarian</small>
                            </a>
                        </div>
                    </div>
                @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
