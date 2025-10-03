@extends('layouts.app')

@section('title', 'Dashboard Dapur')

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Welcome Card -->
        <div class="card mb-4 shadow-sm">
            <div class="card-header">
                <h4 class="mb-0">
                    <i class="bi bi-speedometer2 me-2"></i>Dashboard Dapur
                </h4>
            </div>
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h5 class="text-success">Selamat datang, <strong>{{ auth()->user()->name }}</strong>!</h5>
                        <p class="mb-0">Anda login sebagai 
                            <span class="badge bg-success">{{ ucfirst(auth()->user()->role) }}</span>
                        </p>
                    </div>
                    <div class="col-md-4 text-end">
                        <i class="bi bi-shop display-1 text-success opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="row">
            <div class="col-md-4 mb-3">
                <div class="card h-100 shadow-sm stat-card">
                    <div class="card-body text-center">
                        <div class="display-4 text-info mb-3">
                            <i class="bi bi-eye"></i>
                        </div>
                        <h5 class="card-title">Lihat Bahan Baku</h5>
                        <p class="card-text text-muted">Cek stok dan informasi bahan baku</p>
                        <a href="{{ route('dapur.bahan-baku.index') }}" class="btn btn-info">
                            <i class="bi bi-arrow-right"></i> Lihat Stok
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4 mb-3">
                <div class="card h-100 shadow-sm stat-card">
                    <div class="card-body text-center">
                        <div class="display-4 text-primary mb-3">
                            <i class="bi bi-plus-circle"></i>
                        </div>
                        <h5 class="card-title">Buat Permintaan</h5>
                        <p class="card-text text-muted">Ajukan permintaan bahan baku baru</p>
                        <a href="{{ route('dapur.permintaan.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus"></i> Buat Permintaan
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4 mb-3">
                <div class="card h-100 shadow-sm stat-card">
                    <div class="card-body text-center">
                        <div class="display-4 text-warning mb-3">
                            <i class="bi bi-clock-history"></i>
                        </div>
                        <h5 class="card-title">Riwayat Permintaan</h5>
                        <p class="card-text text-muted">Lihat status permintaan sebelumnya</p>
                        <a href="{{ route('dapur.permintaan.index') }}" class="btn btn-warning">
                            <i class="bi bi-arrow-right"></i> Lihat Riwayat
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection