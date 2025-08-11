@extends('layouts.app')

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
                    <h1>Mahasiswa</h1>
                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="row mb-3">
                            <label for="nim" class="col-md-4 col-form-label text-md-end">NIM</label>

                            <div class="col-md-6">
                                <input 
                                    id="nim" 
                                    type="text" 
                                    class="form-control @error('nim') is-invalid @enderror" 
                                    name="nim" 
                                    required 
                                    autocomplete="off" 
                                    autofocus>
                                @error('nim')
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
                    <div class="card-title"><strong>Selamat datang di eBASIS aplikasi Baca Skripsi</strong></div>
                    <p>Gunakan <strong>NIM</strong> untuk login</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
