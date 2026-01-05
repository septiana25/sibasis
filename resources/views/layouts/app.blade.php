<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <noscript>
        <meta http-equiv="refresh" content="0;url={{ route('nojs') }}">
    </noscript>
    <title>{{ config('app.name', 'Laravel') }}</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <!-- Favicon link -->
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon"> 

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    <link href="{{ asset('bootstrap-icons/bootstrap-icons.min.css') }}" rel="stylesheet">

    <style>
        
    </style>

    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>
<body>
    <div id="app">
        <!-- Modern Navbar -->
        <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">
            <div class="container">                
                <a class="navbar-brand d-flex align-items-center" href="{{ url('/skripsi') }}">
                    <div class="brand-icon me-3">
                        <i class="bi bi-book text-primary fs-2"></i>
                    </div>
                    <div class="brand-text">
                        <span class="fw-bold fs-4 text-dark">{{ config('app.name', 'Laravel') }}</span>
                        <div class="brand-subtitle text-muted small">Perpus Pusat UIN SSC</div>
                    </div>
                </a>
                
                <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link fw-semibold" href="{{ url('/skripsi') }}">
                                <i class="bi bi-house me-1"></i>Beranda
                            </a>
                        </li>
                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto align-items-center">
                        <!-- Authentication Links -->
                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link fw-semibold" href="{{ route('login') }}">
                                        <i class="bi bi-box-arrow-in-right me-1"></i>{{ __('Login') }}
                                    </a>
                                </li>
                            @endif

                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link fw-semibold" href="{{ route('register') }}">
                                        <i class="bi bi-person-plus me-1"></i>{{ __('Register') }}
                                    </a>
                                </li>
                            @endif
                        @else
                            @auth('web')
                                <li class="nav-item">
                                    <a class="nav-link fw-semibold" href="{{ route('admin.documents.index') }}">
                                        <i class="bi bi-upload me-1"></i>Data Dokumen
                                    </a>
                                </li>
                            @endauth
                            
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle fw-semibold d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    <div class="user-avatar me-2">
                                        <i class="bi bi-person-circle fs-5 text-primary"></i>
                                    </div>
                                    <span>
                                        @auth('web')
                                            {{ Auth::user()->name }}
                                        @endauth
                                        @auth('member')
                                            {{ Auth::guard('member')->user()->member_name }}
                                        @endauth
                                    </span>
                                </a>

                                <div class="dropdown-menu dropdown-menu-end border-0 shadow-lg" aria-labelledby="navbarDropdown">
                                    @auth('member')    
                                        <a class="dropdown-item d-flex align-items-center" href="{{ route('member.dashboard') }}">
                                            <i class="bi bi-speedometer2 me-2"></i>{{ __('Dashboard') }}
                                        </a>
                                    @endauth
                                    <hr class="dropdown-divider">
                                    <a class="dropdown-item d-flex align-items-center text-danger" href="
                                        @auth('web')
                                            {{ route('admin.logout') }}
                                        @endauth
                                        @auth('member')
                                            {{ route('logout') }}
                                        @endauth
                                    "
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        <i class="bi bi-box-arrow-right me-2"></i>{{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                        
                        <!-- Theme Toggle -->
                        <li class="nav-item ms-3">
                            <button class="btn btn-outline-primary btn-sm rounded-circle" onclick="toggleDarkMode()" title="Toggle Theme">
                                <i id="darkModeIcon" class="bi bi-brightness-high"></i>
                            </button>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
        
        <main>
            @if (session('error'))
            <div class="container mt-4">
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <div>
                            <strong>
                                @auth('web')
                                    {{ Auth::user()->name }}
                                @endauth
                                @auth('member')
                                    {{ Auth::guard('member')->user()->member_name }}
                                @endauth
                            </strong> {{ session('error') }}
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
            @endif
            
            @if (session('success'))
            <div class="container mt-4">
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-check-circle me-2"></i>
                        <div>{{ session('success') }}</div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
            @endif
            
            @yield('content')
        </main>

        <!-- Modern Footer -->
        <footer class="bg-light border-top mt-auto">
            <div class="container py-4">
                <div class="row align-items-center">
                    <div class="col-md-6 text-center text-md-start">
                        <p class="mb-0 text-muted">
                            &copy; 2025 <strong>UIN Siber Syekh Nurjati Cirebon</strong>
                        </p>
                    </div>
                    <div class="col-md-6 text-center text-md-end">
                        <p class="mb-0 text-muted">
                            Code by <a href="https://lupakoding.wordpress.com/" target="_blank" class="text-decoration-none fw-bold">Ian Septiana</a>
                        </p>
                    </div>
                </div>
            </div>
        </footer>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const body = document.body;
            function handleScroll() {
                if (window.scrollY > 50) {
                    body.classList.add('scrolled');
                } else {
                    body.classList.remove('scrolled');
                }
            }
            handleScroll(); // run once on load
            window.addEventListener('scroll', handleScroll);
        });
    </script>
    <!-- ====== Proteksi UI ABATA ====== -->
    <script>
        let violationCount = 0;

        function registerViolation(eventType) {
            violationCount++;

            Swal.fire({
                icon: 'warning',
                title: 'Peringatan!',
                text: `Aksi "${eventType}" tidak diizinkan. Percobaan ${violationCount}/3 telah dicatat.`,
                confirmButtonColor: '#2563eb'
            });

            // Jika pelanggaran ke-3, kirim data ke server dan logout
            if (violationCount >= 3) {
                fetch('/violation-log', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ count: violationCount })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.redirect) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Anda telah dikeluarkan!',
                            text: 'Sesi Anda telah diakhiri karena pelanggaran berulang.',
                            confirmButtonColor: '#dc2626'
                        }).then(() => {
                            window.location.href = data.redirect;
                        });
                    }
                });
            }
        }
        // ==== Disable Right Click ====
        document.addEventListener('contextmenu', e => {
            e.preventDefault()
            registerViolation('Right Click');
        });

        // ==== Disable Keyboard Shortcuts ====
        document.addEventListener('keydown', e => {
            if (
                e.ctrlKey && ['u','s','x','U','S','X'].includes(e.key) ||
                e.key === 'F12' ||
                (e.ctrlKey && e.shiftKey && ['I','J'].includes(e.key))
            ) {
                e.preventDefault();
                registerViolation('Keyboard Shortcut');
            }
        });
        // ==== Disable Copy, Cut, Paste, Select ====
        /* ['copy'].forEach(evt =>
            document.addEventListener(evt, e => {e.preventDefault(); registerViolation(evt);})
        ); */
    </script>
    <!-- ====== End Proteksi ====== -->

    <script src="{{ asset('js/darkmode.js') }}"></script>
    
    <!-- Custom Navbar Styles -->
    <style>

    </style>
</body>
</html>
