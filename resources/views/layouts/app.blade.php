<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <!-- Favicon link -->
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon"> 

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    <link href="{{ asset('bootstrap-icons/bootstrap-icons.min.css') }}" rel="stylesheet">


    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">                
                <a class="navbar-brand" href="{{ url('/skripsi') }}">
                    <div>
                    <i class="bi bi-book fs-3"></i>
                    <span class="fs-3 fw-bolder">{{ config('app.name', 'Laravel') }}</span>
                    </div>
                
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                                <h4 class="m-0">Perpus Pusat UIN SSC</h4>   
                        </li>
                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto">
                        <!-- Authentication Links -->
                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                                </li>
                            @endif

                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif
                        @else
                            @auth('web')
                                <li class="nav-item">
                                        <a class="nav-link" href="">Upload File</a>
                                </li>
                            @endauth
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle fs-6" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    @auth('web')
                                        {{ Auth::user()->name }}
                                    @endauth
                                    @auth('member')
                                        {{ Auth::guard('member')->user()->member_name }}
                                    @endauth
                                    
                                </a>

                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    @auth('member')    
                                        <a class="dropdown-item" href="{{ route('member.dashboard') }}">
                                            {{ __('Dashboard') }}
                                        </a>
                                    @endauth
                                    <a class="dropdown-item" href="
                                        @auth('web')
                                            {{ route('admin.logout') }}
                                        @endauth
                                        @auth('member')
                                            {{ route('logout') }}
                                        @endauth
                                    "
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </div>
                    <div class="d-flex align-items-center ms-3">
                        <button class="btn btn-lg p-0" onclick="toggleDarkMode()" style="line-height: 1;">
                            <i id="darkModeIcon" class="bi bi-brightness-high"></i>
                        </button>
                    </div>
                </ul>
            </div>
        </nav>
        
        <main class="py-3">
            @if (session('error'))
            <div class="container mt-4">
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>{{ Auth::user()->name }}
                        @auth('member')
                            {{ Auth::guard('member')->user()->member_name }}
                        @endauth</strong> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                  </div>
            </div>
            @endif
            @yield('content')
        </main>

            <!-- Footer -->
        <footer class="text-center text-lg-start bg-body-tertiary text-muted">
            <!-- Section: Social media -->
            <!-- Section: Social media -->
    
            <!-- Section: Links  -->

            <!-- Section: Links  -->
    
            <!-- Copyright -->
            <div class="text-center p-4" style="background-color: rgba(0, 0, 0, 0.05);">
                &copy; 2025 Copyright: <a href="https://uinssc.ac.id" target="_blank">UIN Siber Syekh Nurjati Cirebon</a>. Code By
                <a class="text-reset fw-bold" href="https://lupakoding.wordpress.com/" target="_blank">Ian Septiana</a>
            </div>
            <!-- Copyright -->
        </footer>
            <!-- Footer -->
    </div>
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
    <script src="{{ asset('js/darkmode.js') }}"></script>
    
</body>
</html>
