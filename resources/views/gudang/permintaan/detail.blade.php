@extends('layouts.app')

@section('title', 'Detail Permintaan - Gudang')

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Page Header -->
        <div class="card mb-4 shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1">
                        <i class="bi bi-clipboard-data me-2"></i>Detail Permintaan Bahan Baku
                    </h4>
                    <p class="mb-0 text-muted">Informasi lengkap permintaan dan ketersediaan stok</p>
                </div>
                <a href="{{ route('gudang.permintaan.status') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-2"></i>Kembali ke Daftar
                </a>
            </div>
        </div>

        <!-- Request Information -->
        <div class="row mb-4">
            <!-- Pemohon Info -->
            <div class="col-md-6">
                <div class="card h-100 shadow-sm">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="bi bi-person-circle me-2"></i>Informasi Pemohon
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 mb-3">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-person me-3 text-primary"></i>
                                    <div>
                                        <small class="text-muted">Nama Pemohon</small>
                                        <div class="fw-bold">{{ $permintaan->pemohon_name }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-envelope me-3 text-primary"></i>
                                    <div>
                                        <small class="text-muted">Email</small>
                                        <div class="fw-bold">{{ $permintaan->pemohon_email }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Request Details -->
            <div class="col-md-6">
                <div class="card h-100 shadow-sm">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="bi bi-clipboard-check me-2"></i>Detail Permintaan
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="bi bi-clipboard-data me-3 text-success"></i>
                                    <div>
                                        <small class="text-muted">Menu Makan</small>
                                        <div class="fw-bold">{{ $permintaan->menu_makan }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="bi bi-people me-3 text-info"></i>
                                    <div>
                                        <small class="text-muted">Jumlah Porsi</small>
                                        <div class="fw-bold">{{ $permintaan->jumlah_porsi }} porsi</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="bi bi-calendar3 me-3 text-warning"></i>
                                    <div>
                                        <small class="text-muted">Tanggal Masak</small>
                                        <div class="fw-bold">{{ date('d/m/Y', strtotime($permintaan->tgl_masak)) }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="bi bi-clock me-3 text-secondary"></i>
                                    <div>
                                        <small class="text-muted">Tanggal Permintaan</small>
                                        <div class="fw-bold">{{ date('d/m/Y H:i', strtotime($permintaan->created_at)) }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-flag me-3"></i>
                                    <div>
                                        <small class="text-muted">Status</small>
                                        <div>
                                            @if($permintaan->status == 'menunggu')
                                            <span class="badge bg-warning">
                                                <i class="bi bi-clock me-1"></i>Menunggu
                                            </span>
                                            @elseif($permintaan->status == 'disetujui')
                                            <span class="badge bg-success">
                                                <i class="bi bi-check-circle me-1"></i>Disetujui
                                            </span>
                                            @elseif($permintaan->status == 'ditolak')
                                            <span class="badge bg-danger">
                                                <i class="bi bi-x-circle me-1"></i>Ditolak
                                            </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Materials Required -->
        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-list-check me-2"></i>Daftar Bahan Baku yang Diperlukan
                </h5>
            </div>
            <div class="card-body">
                @if(isset($details) && count($details) > 0)
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th scope="col" class="text-center">#</th>
                                <th scope="col">Nama Bahan Baku</th>
                                <th scope="col" class="text-center">Jumlah Diminta</th>
                                <th scope="col" class="text-center">Stok Tersedia</th>
                                <th scope="col" class="text-center">Selisih</th>
                                <th scope="col" class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($details as $index => $detail)
                                <tr>
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-box-seam me-2 text-primary"></i>
                                            <strong>{{ $detail->bahan_nama }}</strong>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-primary">
                                            {{ $detail->jumlah_diminta }} {{ $detail->satuan }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-info">
                                            {{ $detail->stok_tersedia }} {{ $detail->satuan }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        @php
                                            $selisih = $detail->stok_tersedia - $detail->jumlah_diminta;
                                        @endphp
                                        <span class="badge {{ $selisih >= 0 ? 'bg-success' : 'bg-danger' }}">
                                            {{ $selisih >= 0 ? '+' : '' }}{{ $selisih }} {{ $detail->satuan }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        @if($detail->stok_tersedia >= $detail->jumlah_diminta)
                                            <span class="badge bg-success">
                                                <i class="bi bi-check-circle me-1"></i>Stok Cukup
                                            </span>
                                        @else
                                            <span class="badge bg-danger">
                                                <i class="bi bi-x-circle me-1"></i>Stok Kurang
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Summary Card -->
                @php
                    $totalItems = count($details);
                    $availableItems = 0;
                    foreach($details as $detail) {
                        if($detail->stok_tersedia >= $detail->jumlah_diminta) {
                            $availableItems++;
                        }
                    }
                @endphp
                
                <div class="alert {{ $availableItems == $totalItems ? 'alert-success' : 'alert-warning' }} mt-4">
                    <div class="d-flex align-items-center mb-3">
                        <i class="bi bi-clipboard-data display-6 me-3"></i>
                        <div>
                            <h5 class="mb-1">Ringkasan Ketersediaan Bahan</h5>
                            <p class="mb-0">Analisis stok untuk semua bahan yang diminta</p>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4 mb-2">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-list-ul me-2"></i>
                                <strong>Total Bahan: {{ $totalItems }} jenis</strong>
                            </div>
                        </div>
                        <div class="col-md-4 mb-2">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-check-circle me-2 text-success"></i>
                                <strong>Bahan Tersedia: {{ $availableItems }} jenis</strong>
                            </div>
                        </div>
                        <div class="col-md-4 mb-2">
                            @if($availableItems == $totalItems)
                                <div class="d-flex align-items-center text-success">
                                    <i class="bi bi-check-circle-fill me-2"></i>
                                    <strong>✓ Semua bahan dapat dipenuhi</strong>
                                </div>
                            @else
                                <div class="d-flex align-items-center text-warning">
                                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                    <strong>⚠ {{ $totalItems - $availableItems }} bahan tidak mencukupi</strong>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                @else
                <div class="text-center py-5">
                    <div class="mb-4">
                        <i class="bi bi-inbox display-1 text-muted"></i>
                    </div>
                    <h4 class="text-muted mb-3">Tidak Ada Detail Bahan</h4>
                    <p class="text-muted">Permintaan ini belum memiliki detail bahan baku yang diperlukan.</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="card mt-4 shadow-sm">
            <div class="card-body text-center">
                <div class="d-flex gap-3 justify-content-center align-items-center flex-wrap">
                    <a href="{{ route('gudang.permintaan.status') }}" class="btn btn-outline-secondary btn-lg">
                        <i class="bi bi-arrow-left me-2"></i>Kembali ke Daftar
                    </a>
                    
                    @if($permintaan->status == 'menunggu')
                        <form method="POST" action="{{ route('gudang.permintaan.approve', $permintaan->id) }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-success btn-lg" 
                                    onclick="return confirm('Yakin ingin menyetujui permintaan ini?\n\nStok bahan baku akan dikurangi otomatis sesuai jumlah yang diminta.')">
                                <i class="bi bi-check-circle me-2"></i>Setujui Permintaan
                            </button>
                        </form>
                        
                        <form method="POST" action="{{ route('gudang.permintaan.reject', $permintaan->id) }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-danger btn-lg" 
                                    onclick="return confirm('Yakin ingin menolak permintaan ini?\n\nAksi ini tidak dapat dibatalkan.')">
                                <i class="bi bi-x-circle me-2"></i>Tolak Permintaan
                            </button>
                        </form>
                    @else
                        <div class="alert {{ $permintaan->status == 'disetujui' ? 'alert-success' : 'alert-danger' }} mb-0">
                            <div class="d-flex align-items-center justify-content-center">
                                @if($permintaan->status == 'disetujui')
                                    <i class="bi bi-check-circle-fill me-2"></i>
                                    <strong>Permintaan Sudah Disetujui</strong>
                                @else
                                    <i class="bi bi-x-circle-fill me-2"></i>
                                    <strong>Permintaan Sudah Ditolak</strong>
                                @endif
                            </div>
                            <div class="mt-1 text-center">
                                <small>Aksi telah selesai pada {{ date('d/m/Y H:i', strtotime($permintaan->updated_at)) }}</small>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection