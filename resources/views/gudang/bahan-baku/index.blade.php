@extends('layouts.app')

@section('title', 'Kelola Bahan Baku')

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Page Header -->
        <div class="card mb-4 shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1">
                        <i class="bi bi-box-seam me-2"></i>Kelola Bahan Baku
                    </h4>
                    <p class="mb-0 text-muted">Daftar semua bahan baku yang tersedia di gudang</p>
                </div>
                <a href="{{ route('gudang.bahan-baku.create') }}" class="btn btn-success">
                    <i class="bi bi-plus-circle me-2"></i>Tambah Bahan Baku
                </a>
            </div>
        </div>

        <!-- Data Table -->
        <div class="card shadow-sm">
            <div class="card-body">
                @if(count($bahanBaku) > 0)
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Nama Bahan</th>
                                <th scope="col">Kategori</th>
                                <th scope="col">Stok</th>
                                <th scope="col">Satuan</th>
                                <th scope="col">Tanggal Masuk</th>
                                <th scope="col">Tanggal Kadaluarsa</th>
                                <th scope="col">Status</th>
                                <th scope="col" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($bahanBaku as $index => $bahan)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <strong>{{ $bahan->nama }}</strong>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ $bahan->kategori }}</span>
                                </td>
                                <td>
                                    <strong class="text-primary">{{ number_format($bahan->jumlah, 0) }}</strong>
                                </td>
                                <td>{{ $bahan->satuan }}</td>
                                <td>
                                    <small class="text-muted">
                                        <i class="bi bi-calendar3 me-1"></i>
                                        {{ date('d/m/Y', strtotime($bahan->tanggal_masuk)) }}
                                    </small>
                                </td>
                                <td>
                                    <small class="text-muted">
                                        <i class="bi bi-calendar-x me-1"></i>
                                        {{ date('d/m/Y', strtotime($bahan->tanggal_kadaluarsa)) }}
                                    </small>
                                </td>
                                <td>
                                    @if($bahan->status == 'tersedia')
                                    <span class="badge bg-success">
                                        <i class="bi bi-check-circle me-1"></i>Tersedia
                                    </span>
                                    @elseif($bahan->status == 'segera_kadaluarsa')
                                    <span class="badge bg-warning">
                                        <i class="bi bi-exclamation-triangle me-1"></i>Segera Kadaluarsa
                                    </span>
                                    @elseif($bahan->status == 'kadaluarsa')
                                    <span class="badge bg-danger">
                                        <i class="bi bi-x-circle me-1"></i>Kadaluarsa
                                    </span>
                                    @else
                                    <span class="badge bg-secondary">
                                        <i class="bi bi-dash-circle me-1"></i>Habis
                                    </span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('gudang.bahan-baku.edit', $bahan->id) }}" 
                                           class="btn btn-primary btn-sm" title="Edit Bahan Baku">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        @if($bahan->status == 'kadaluarsa')
                                        <form method="POST" action="{{ route('gudang.bahan-baku.delete', $bahan->id) }}" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" 
                                                    title="Hapus Bahan Kadaluarsa"
                                                    onclick="return confirm('Apakah Anda yakin ingin menghapus bahan baku kadaluarsa ini?')">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @else
                <div class="text-center py-5">
                    <div class="mb-4">
                        <i class="bi bi-inbox display-1 text-muted"></i>
                    </div>
                    <h4 class="text-muted mb-3">Belum Ada Bahan Baku</h4>
                    <p class="text-muted mb-4">Mulai dengan menambah bahan baku pertama Anda</p>
                    <a href="{{ route('gudang.bahan-baku.create') }}" class="btn btn-success btn-lg">
                        <i class="bi bi-plus-circle me-2"></i>Tambah Bahan Baku Pertama
                    </a>
                </div>
                @endif
            </div>
        </div>

        <!-- Status Information -->
        <div class="card mt-4 shadow-sm">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-info-circle me-2"></i>Penjelasan Status
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <span class="badge bg-success me-2">
                            <i class="bi bi-check-circle me-1"></i>Tersedia
                        </span>
                        Stok > 0 dan masih dalam masa konsumsi
                    </div>
                    <div class="col-md-6 mb-3">
                        <span class="badge bg-warning me-2">
                            <i class="bi bi-exclamation-triangle me-1"></i>Segera Kadaluarsa
                        </span>
                        Akan kadaluarsa dalam ≤ 3 hari
                    </div>
                    <div class="col-md-6 mb-3">
                        <span class="badge bg-danger me-2">
                            <i class="bi bi-x-circle me-1"></i>Kadaluarsa
                        </span>
                        Sudah melewati tanggal kadaluarsa (bisa dihapus)
                    </div>
                    <div class="col-md-6 mb-3">
                        <span class="badge bg-secondary me-2">
                            <i class="bi bi-dash-circle me-1"></i>Habis
                        </span>
                        Stok = 0
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection