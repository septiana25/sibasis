@extends('layouts.app')

@section('title', $data['judul_ta'])

@section('content')

<div class="container mt-4">
    <a href="{{ route('skripsi.index') }}" class="btn btn-sm btn-outline-secondary mb-3">← Kembali</a>
    <div class="card">
        <div class="card-body">
            <div class="card-title d-flex justify-content-between">
                <h4 class="text-success-emphasis text-uppercase"><strong>{{ $data['judul_ta'] }}</strong></h4>
                <a href="{{ route('skripsi.bookmark', $index) }}" class="link-danger">
                    <h3 id="bookmark"><i class="bi bi-bookmark{{ ($data['bookmarked'] ?? false) ? '-fill' : '' }}"></i></h3>
                </a>
            </div>
            <p class="mb-1"><strong>Penulis:</strong> {{ $data['nama'] }}</p>
            <p class="mb-1"><strong>Nim:</strong> {{ $data['nim'] }}</p>
            <p class="mb-1"><strong>Jurusan:</strong> {{ $data['program_studi'] }}</p>
            <p class="mb-3"><strong>Fakultas:</strong> {{ $data['fakultas'] }}</p>


            <h5><i class="bi bi-file-earmark-pdf"></i> Pratinjau Skripsi (PDF)</h5>
            <div class="position-relative border p-2 rounded" style="height: 100vh; overflow: hidden;">
    
                <!-- Placeholder Boostrap 5.3 -->
                <div id="iframe-placeholder" class="position-absolute top-0 start-0 w-100 h-100 p-3">
                    <div class="placeholder-glow h-100 w-100 rounded bg-light">
                        <div class="h-100 w-100 placeholder"></div>
                    </div>
                </div>
                {{-- pdf.js --}}
                <iframe id="viewer-frame"
                        src="{{ asset('pdfjs/web/viewer.html') }}?file={{ urlencode(route('skripsi.stream', ['index' => $index])) }}"
                        width="100%"
                        height="100%"
                        frameborder="0"
                        sandbox="allow-scripts allow-same-origin"
                        onload="document.getElementById('iframe-placeholder').style.display='none';">
                </iframe>
            
                <!-- Iframe PDF -->
                {{-- <iframe 
                    src="{{ asset($pdf['url_file']) }}#toolbar=0&navpanes=0&scrollbar=0" 
                    width="100%" 
                    height="100%" 
                    frameborder="0"
                    onload="document.getElementById('iframe-placeholder').style.display='none';">
                </iframe> --}}
            </div>
            
            
            <small class="text-muted text-danger-emphasis">* File ini hanya dapat dibaca, tidak dapat diunduh atau dicetak.</small>
        </div>
    </div>
</div>

  <script>
    // Block print shortcut globally (best-effort). PDF.js may still respond, but ini menambah lapisan.
    window.addEventListener('keydown', function(e){
      if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'p') {
        e.preventDefault();
        alert('Print disabled for this document.');
      }
    });

    // Nonaktifkan context menu on top-level page (iframe viewer akan di-handle di viewer.html juga)
    window.addEventListener('contextmenu', function(e){
      // jangan block context menu site-wide jika user butuh. Ini opsional.
      // e.preventDefault();
    });
  </script>
@endsection
