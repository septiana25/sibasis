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
                                        <td><a href="{{ route('skripsi.show', $bookmark->nim) }}" class="text-decoration-none text-light-emphasis text-uppercase" target="_blank">
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
                                        <td><a href="{{ route('skripsi.show', $static->nim) }}" class="text-decoration-none text-light-emphasis text-uppercase" target="_blank">
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
@endsection
