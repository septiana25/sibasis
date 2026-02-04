@extends('layouts.app')
@section('title', 'Dashboard Mahasiswa')
@section('content')
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-12">
                <div class="my-3">
                    <h4><strong> Hai {{ $name }}! Selamat Datang Di Dashboard</strong></h4>
                    <p>Berikut daftar statistik kamu</p>
                </div>
                <div class="card">
                    <div class="card-body">
                        <div class="card-title text-success-emphasis">
                            <h4><strong><i class="bi bi-heart"></i> Daftar Judul Favorit</strong></h4>
                            <hr>
                        </div>
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th scope="col">No</th>
                                    <th scope="col">Judul</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($bookmarks as $index => $bookmark)
                                    <tr>
                                        <th scope="row">{{ $index + 1 }}</th>
                                        <td><a href="#" class="text-decoration-none text-light-emphasis"
                                                onclick="submitPostToBlank('{{ route('skripsi.search') }}', '{{ $bookmark->judul_ta }}'); return false;">
                                                {{ $bookmark->judul_ta }}
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-12 mt-3">
                <div class="card">
                    <div class="card-body">
                        <div class="card-title text-success-emphasis">
                            <h4><strong><i class="bi bi-graph-up-arrow"></i> Daftar Yang Sering Dibaca</strong></h4>
                            <hr>
                        </div>
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th scope="col">No</th>
                                    <th scope="col">Judul</th>
                                    <th scope="col">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($statistic as $index => $static)
                                    <tr>
                                        <th scope="row">{{ $index + 1 }}</th>
                                        <td><a href="#" class="text-decoration-none text-light-emphasis"
                                                onclick="submitPostToBlank('{{ route('skripsi.search') }}', '{{ $static->judul_ta }}'); return false;">
                                                {{ $static->judul_ta }}
                                            </a>
                                        </td>
                                        <td>{{ $static->total }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function submitPostToBlank(action, judul) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = action;
            form.target = '_blank';

            // CSRF token
            const token = document.createElement('input');
            token.type = 'hidden';
            token.name = '_token';
            token.value = '{{ csrf_token() }}';
            form.appendChild(token);

            // Input judul
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'judul';
            input.value = judul;
            form.appendChild(input);

            document.body.appendChild(form);
            form.submit();
            document.body.removeChild(form);
        }
    </script>
@endsection
