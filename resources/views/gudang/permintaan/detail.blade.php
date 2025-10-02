@extends('layouts.app')

@section('title', 'Detail Permintaan - Gudang')

@section('content')
<style>
    .text-success {
        color: #27ae60 !important;
    }
    .text-danger {
        color: #e74c3c !important;
    }
</style>
<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2>Detail Permintaan Bahan Baku</h2>
        <a href="{{ route('gudang.permintaan.status') }}" class="btn btn-secondary">← Kembali ke Daftar</a>
    </div>
</div>

<div class="card">
    <!-- Header Informasi Permintaan -->
    <div style="background-color: #f8f9fa; padding: 20px; border-radius: 5px; margin-bottom: 20px;">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div>
                <h4 style="color: #2c3e50; margin-bottom: 15px;">Informasi Pemohon</h4>
                <p><strong>Nama:</strong> {{ $permintaan->pemohon_name }}</p>
                <p><strong>Email:</strong> {{ $permintaan->pemohon_email }}</p>
            </div>
            <div>
                <h4 style="color: #2c3e50; margin-bottom: 15px;">Detail Permintaan</h4>
                <p><strong>Menu Makan:</strong> {{ $permintaan->menu_makan }}</p>
                <p><strong>Jumlah Porsi:</strong> {{ $permintaan->jumlah_porsi }} porsi</p>
                <p><strong>Tanggal Masak:</strong> {{ date('d/m/Y', strtotime($permintaan->tgl_masak)) }}</p>
                <p><strong>Tanggal Permintaan:</strong> {{ date('d/m/Y H:i', strtotime($permintaan->created_at)) }}</p>
                <p><strong>Status:</strong> 
                    @if($permintaan->status == 'menunggu')
                    <span class="badge badge-warning">Menunggu</span>
                    @elseif($permintaan->status == 'disetujui')
                    <span class="badge badge-success">Disetujui</span>
                    @elseif($permintaan->status == 'ditolak')
                    <span class="badge badge-danger">Ditolak</span>
                    @endif
                </p>
            </div>
        </div>
    </div>

    <!-- Tabel Detail Bahan Baku -->
    <h4 style="color: #2c3e50; margin-bottom: 15px;">Daftar Bahan Baku yang Diperlukan</h4>
    
    @if(isset($details) && count($details) > 0)
        <div style="overflow-x: auto;">
            <table class="table" style="border: 1px solid #ddd;">
                <thead>
                    <tr style="background-color: #e9ecef;">
                        <th style="border: 1px solid #ddd; text-align: center; width: 50px;">No</th>
                        <th style="border: 1px solid #ddd;">Nama Bahan Baku</th>
                        <th style="border: 1px solid #ddd; text-align: center;">Jumlah Diminta</th>
                        <th style="border: 1px solid #ddd; text-align: center;">Stok Tersedia</th>
                        <th style="border: 1px solid #ddd; text-align: center;">Selisih</th>
                        <th style="border: 1px solid #ddd; text-align: center;">Status Ketersediaan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($details as $index => $detail)
                        <tr>
                            <td style="border: 1px solid #ddd; text-align: center;">{{ $index + 1 }}</td>
                            <td style="border: 1px solid #ddd; padding: 12px;">
                                <strong>{{ $detail->bahan_nama }}</strong>
                            </td>
                            <td style="border: 1px solid #ddd; text-align: center; padding: 12px;">
                                {{ $detail->jumlah_diminta }} {{ $detail->satuan }}
                            </td>
                            <td style="border: 1px solid #ddd; text-align: center; padding: 12px;">
                                {{ $detail->stok_tersedia }} {{ $detail->satuan }}
                            </td>
                            <td style="border: 1px solid #ddd; text-align: center; padding: 12px;">
                                @php
                                    $selisih = $detail->stok_tersedia - $detail->jumlah_diminta;
                                @endphp
                                <span class="{{ $selisih >= 0 ? 'text-success' : 'text-danger' }}" style="font-weight: bold;">
                                    {{ $selisih >= 0 ? '+' : '' }}{{ $selisih }} {{ $detail->satuan }}
                                </span>
                            </td>
                            <td style="border: 1px solid #ddd; text-align: center; padding: 12px;">
                                @if($detail->stok_tersedia >= $detail->jumlah_diminta)
                                    <span class="badge badge-success">Stok Cukup</span>
                                @else
                                    <span class="badge badge-danger">Stok Kurang</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <!-- Summary -->
        <div style="margin-top: 20px; padding: 15px; background-color: #f1f3f4; border-radius: 5px; border-left: 4px solid #007bff;">
            @php
                $totalItems = count($details);
                $availableItems = 0;
                foreach($details as $detail) {
                    if($detail->stok_tersedia >= $detail->jumlah_diminta) {
                        $availableItems++;
                    }
                }
            @endphp
            <h5 style="margin-bottom: 10px; color: #2c3e50;">Ringkasan Ketersediaan</h5>
            <p style="margin-bottom: 8px;">
                <strong>Total Bahan:</strong> {{ $totalItems }} jenis
            </p>
            <p style="margin-bottom: 8px;">
                <strong>Bahan Tersedia:</strong> {{ $availableItems }} jenis
            </p>
            <p style="margin-bottom: 0;">
                <strong>Status:</strong> 
                @if($availableItems == $totalItems)
                    <span style="color: #27ae60; font-weight: bold;">✓ Semua bahan dapat dipenuhi</span>
                @else
                    <span style="color: #e74c3c; font-weight: bold;">⚠ Ada {{ $totalItems - $availableItems }} bahan yang tidak mencukupi</span>
                @endif
            </p>
        </div>
    @else
        <div style="text-align: center; padding: 40px 20px; background-color: #f8f9fa; border-radius: 5px;">
            <h4 style="color: #666; margin-bottom: 10px;">Tidak Ada Detail Bahan</h4>
            <p style="color: #888;">Permintaan ini belum memiliki detail bahan baku yang diperlukan.</p>
        </div>
    @endif
</div>

<!-- Action Buttons (jika diperlukan untuk challenge feature nanti) -->
<div class="card" style="text-align: center;">
    <div style="display: flex; gap: 10px; justify-content: center;">
        <a href="{{ route('gudang.permintaan.status') }}" class="btn btn-secondary">← Kembali ke Daftar</a>
        <!-- Tombol approve/reject bisa ditambahkan di sini untuk challenge feature -->
    </div>
</div>
@endsection