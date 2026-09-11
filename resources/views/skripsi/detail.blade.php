@extends('layouts.app')

@section('title', $data['judul_ta'])

@section('content')

    <div class="content-section">
        <div class="container">
            <a href="{{ route('skripsi.index') }}" class="btn btn-sm btn-outline-primary mb-3">← Kembali</a>

            <!-- Detail Card aligned with index result-card style -->
            <div class="result-card">
                <div class="card-header d-flex justify-content-between align-items-start">
                    <div class="d-flex align-items-center">
                        <div class="card-icon">
                            <i class="bi bi-file-earmark-text"></i>
                        </div>
                        <div class="card-title ms-2">
                            <span class="title-link text-uppercase">{{ $data['judul_ta'] }}</span>
                        </div>
                    </div>
                    <a href="{{ route('skripsi.bookmark', $nim) }}" class="link-danger" aria-label="Bookmark">
                        <h3 id="bookmark" class="mb-0"><i
                                class="bi bi-bookmark{{ $data['bookmarked'] ?? false ? '-fill' : '' }}"></i></h3>
                    </a>
                </div>

                <div class="card-body">
                    <div class="meta-grid">
                        <div class="meta-item">
                            <i class="bi bi-person"></i>
                            <span class="meta-label">Penulis:</span>
                            <span class="meta-value">{{ $data['nama'] }}</span>
                        </div>
                        <div class="meta-item">
                            <i class="bi bi-person-circle"></i>
                            <span class="meta-label">NIM:</span>
                            <span class="meta-value">{{ $data['nim'] }}</span>
                        </div>
                        <div class="meta-item">
                            <i class="bi bi-mortarboard"></i>
                            <span class="meta-label">Program Studi:</span>
                            <span class="meta-value">{{ $data['program_studi'] }}</span>
                        </div>
                        <div class="meta-item">
                            <i class="bi bi-building"></i>
                            <span class="meta-label">Fakultas:</span>
                            <span class="meta-value">{{ $data['fakultas'] }}</span>
                        </div>
                    </div>
                </div>

                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <div class="card-icon">
                            <i class="bi bi-file-earmark-pdf"></i>
                        </div>
                        <div class="card-title ms-2 mb-0">
                            <span class="title-link">Pratinjau Skripsi (PDF)</span>
                        </div>
                    </div>
                    <button type="button" id="tanyaAiButton" class="btn btn-sm btn-tanya-ai text-white" onclick="handleTanyaAi()">
                        <i class="bi bi-stars me-1" id="tanyaAiIcon"></i>Tanya AI
                    </button>
                </div>

                <div class="card-body">
                    <div class="position-relative border p-2 rounded" style="height: 100vh; overflow: hidden;">
                        <!-- Placeholder Boostrap 5.3 -->
                        <div id="iframe-placeholder" class="position-absolute top-0 start-0 w-100 h-100 p-3">
                            <div class="placeholder-glow h-100 w-100 rounded bg-light">
                                <div class="h-100 w-100 placeholder"></div>
                            </div>
                        </div>
                        {{-- pdf.js --}}
                        <iframe 
                            id="viewer-frame"
                            src="{{ asset('pdfjs/web/viewer.html') }}?file={{ urlencode(route('skripsi.stream', ['nim' => $nim])) }}"
                            width="100%" 
                            height="100%" 
                            frameborder="0" 
                            sandbox="allow-scripts allow-same-origin"
                            onload="document.getElementById('iframe-placeholder').style.display='none';">
                        </iframe>
                    </div>
                    <small class="text-muted text-danger-emphasis d-inline-block mt-2">* File ini hanya dapat dibaca, tidak
                        dapat diunduh atau dicetak.</small>
                </div>
            </div>
        </div>
    </div>

    <script>
        function handleTanyaAi() {
            const button = document.getElementById('tanyaAiButton');
            const icon = document.getElementById('tanyaAiIcon');

            button.setAttribute('disabled', 'disabled');
            icon.outerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>';

            const judul = "{!! addslashes($data['judul_ta']) !!}";
            const query = judul + ' jelaskan hasil penelitiannya';
            // Membuka URL di tab baru
            window.open('/redirect-chat-ai?q=' + encodeURIComponent(query), '_blank');

            // Mengembalikan status tombol karena pengguna tidak berpindah halaman
            setTimeout(() => {
                button.removeAttribute('disabled');
                button.innerHTML = '<i class="bi bi-stars me-1" id="tanyaAiIcon"></i>Tanya AI';
            }, 20000);
        }

        // Block print shortcut globally (best-effort). PDF.js may still respond, but ini menambah lapisan.
        window.addEventListener('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'p') {
                e.preventDefault();
                alert('Print disabled for this document.');
            }
        });

        // Nonaktifkan context menu on top-level page (iframe viewer akan di-handle di viewer.html juga)
        window.addEventListener('contextmenu', function(e) {
            // jangan block context menu site-wide jika user butuh. Ini opsional.
            // e.preventDefault();
        });
    </script>
@endsection
