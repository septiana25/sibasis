@extends('layouts.app')

@section('title', 'Login Mahasiswa')
@section('content')
    <div class="container">
        <div class="row justify-content-center align-items-center">
            <div class="col-md-8">
                <div class="text-center my-3">
                    <img src="{{ asset('uinssc_small.png') }}" width="80" class="rounded" alt="...">
                </div>
                <div class="card">
                    <div class="card-header">Login Mahasiswa</div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('member.login') }}">
                            @csrf

                            <div class="row mb-3">
                                <label for="anggota" class="col-md-4 col-form-label text-md-end">NIM</label>

                                <div class="col-md-6">
                                    <input 
                                        id="anggota" 
                                        type="text"
                                        class="form-control 
                                        @error('anggota') is-invalid 
                                        @enderror" 
                                        name="anggota" 
                                        required
                                        autocomplete="off" 
                                        autofocus>
                                    @error('anggota')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="row mb-0">
                                <div class="col-md-8 offset-md-4">
                                    <button type="submit" class="btn btn-dark">
                                        {{ __('Login') }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="card text-center my-3">
                    <div class="card-body">
                        <h1>
                            <i class="bi bi-mortarboard"></i>
                        </h1>
                        <div class="card-title"><strong>Selamat datang di ABATA Aplikasi Baca Skripsi</strong></div>
                        <p>Gunakan <strong>NIM atau No Anggota Perpustakaan</strong> untuk login,</p>
                        {{-- <strong>
                        <a class="text-warning-emphasis link-offset-2 link-underline link-underline-opacity-0" href="https://perpustakaan.uinssc.ac.id/sibasis-jurus-jitu-cari-inspirasi-skripsi-di-perpustakaan-uin-ssc/" target="_blank">Tips menggunakan SIBASIS</a>
                    </strong> --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
