@extends('layouts.app')

@section('title', 'Dashboard Dapur')

@section('content')
<div class="card">
    <h2>Dashboard Dapur</h2>
    <p>Selamat datang, <strong>{{ auth()->user()->name }}</strong>!</p>
    <p>Anda login sebagai <span class="badge badge-success">{{ ucfirst(auth()->user()->role) }}</span></p>
</div>
@endsection