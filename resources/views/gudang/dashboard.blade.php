@extends('layouts.app')

@section('title', 'Dashboard Gudang')

@section('content')
<div class="card">
    <h2>Dashboard Gudang</h2>
    <p>Selamat datang, <strong>{{ auth()->user()->name }}</strong>!</p>
    <p>Anda login sebagai <span class="badge badge-info">{{ ucfirst(auth()->user()->role) }}</span></p>
</div>
@endsection