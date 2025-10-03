@extends('layouts.app')

@section('title', 'Dashboard Gudang')

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Welcome Card -->
        <div class="card mb-4 shadow-sm">
            <div class="card-header">
                <h4 class="mb-0">
                    <i class="bi bi-speedometer2 me-2"></i>Dashboard Gudang
                </h4>
            </div>
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h5 class="text-primary">Selamat datang, <strong>{{ auth()->user()->name }}</strong>!</h5>
                        <p class="mb-0">Anda login sebagai 
                            <span class="badge bg-primary">{{ ucfirst(auth()->user()->role) }}</span>
                        </p>
                    </div>
                    <div class="col-md-4 text-end">
                        <i class="bi bi-person-workspace display-1 text-primary opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="row">
            <div class="col-md-6 mb-3">
                <div class="card h-100 shadow-sm stat-card">
                    <div class="card-body text-center">
                        <div class="display-4 text-success mb-3">
                            <i class="bi bi-box-seam"></i>
                        </div>
                        <h5 class="card-title">Kelola Bahan Baku</h5>
                        <p class="card-text text-muted">Tambah, edit, dan hapus data bahan baku</p>
                        <a href="{{ route('gudang.bahan-baku.index') }}" class="btn btn-success">
                            <i class="bi bi-arrow-right"></i> Lihat Bahan Baku
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 mb-3">
                <div class="card h-100 shadow-sm stat-card">
                    <div class="card-body text-center">
                        <div class="display-4 text-warning mb-3">
                            <i class="bi bi-clipboard-check"></i>
                        </div>
                        <h5 class="card-title">Status Permintaan</h5>
                        <p class="card-text text-muted">Lihat dan proses permintaan dari dapur</p>
                        <a href="{{ route('gudang.permintaan.status') }}" class="btn btn-warning">
                            <i class="bi bi-arrow-right"></i> Lihat Permintaan
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection