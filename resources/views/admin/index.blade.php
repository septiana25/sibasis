@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('content')
    <div class="container mt-4">
        <div class="row row-cols-1 row-cols-md-3 g-4">
        @foreach ($cards as $index => $card)
            <x-card-statistic
            title="{{ $card['title'] }}"
            :icons="[
                'icon' => $card['icons']['icon'],
                'bg' => $card['icons']['bg'],
                'color' => $card['icons']['color'] 
            ]"
            total="{{ $card['total'] }}"
            />
        @endforeach
        </div>
    </div>
@endsection
