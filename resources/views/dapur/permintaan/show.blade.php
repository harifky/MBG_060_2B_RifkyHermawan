@extends('layouts.app')

@section('title', 'Detail Permintaan - Dapur')

@section('content')
<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2>Detail Permintaan Bahan Baku</h2>
        <div>
            <a href="{{ route('dapur.permintaan.index') }}" class="btn btn-secondary">← Kembali</a>
            @if($permintaan->status == 'menunggu')
            <a href="{{ route('dapur.permintaan.edit', $permintaan->id) }}" class="btn btn-warning">Edit</a>
            @endif
        </div>
    </div>
</div>

<div class="card">
    <!-- Header Informasi Permintaan -->
    <div style="background-color: #f8f9fa; padding: 20px; border-radius: 5px; margin-bottom: 20px;">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div>
                <h4 style="color: #2c3e50; margin-bottom: 15px;">Informasi Permintaan</h4>
                <p><strong>Menu Makan:</strong> {{ $permintaan->menu_makan }}</p>
                <p><strong>Jumlah Porsi:</strong> {{ $permintaan->jumlah_porsi }} porsi</p>
                <p><strong>Tanggal Masak:</strong> {{ date('d/m/Y', strtotime($permintaan->tgl_masak)) }}</p>
            </div>
            <div>
                <h4 style="color: #2c3e50; margin-bottom: 15px;">Status & Waktu</h4>
                <p><strong>Status:</strong> 
                    @if($permintaan->status == 'menunggu')
                    <span class="badge badge-warning">Menunggu</span>
                    @elseif($permintaan->status == 'disetujui')
                    <span class="badge badge-success">Disetujui</span>
                    @elseif($permintaan->status == 'ditolak')
                    <span class="badge badge-danger">Ditolak</span>
                    @endif
                </p>
                <p><strong>Tanggal Permintaan:</strong> {{ date('d/m/Y H:i', strtotime($permintaan->created_at)) }}</p>
            </div>
        </div>
    </div>

    <!-- Tabel Detail Bahan Baku -->
    <h4 style="color: #2c3e50; margin-bottom: 15px;">Daftar Bahan yang Diminta</h4>
    
    @if(isset($details) && count($details) > 0)
        <div style="overflow-x: auto;">
            <table class="table" style="border: 1px solid #ddd;">
                <thead>
                    <tr style="background-color: #e9ecef;">
                        <th style="border: 1px solid #ddd; text-align: center; width: 50px;">No</th>
                        <th style="border: 1px solid #ddd;">Nama Bahan Baku</th>
                        <th style="border: 1px solid #ddd; text-align: center;">Jumlah Diminta</th>
                        <th style="border: 1px solid #ddd; text-align: center;">Stok Tersedia</th>
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
                                @if($detail->stok_tersedia >= $detail->jumlah_diminta)
                                    <span class="badge badge-success">Tersedia</span>
                                @else
                                    <span class="badge badge-danger">Kurang</span>
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
                <strong>Kemungkinan Status:</strong> 
                @if($availableItems == $totalItems)
                    <span style="color: #27ae60; font-weight: bold;">✓ Kemungkinan besar akan disetujui</span>
                @else
                    <span style="color: #e74c3c; font-weight: bold;">⚠ Kemungkinan ditolak karena {{ $totalItems - $availableItems }} bahan tidak mencukupi</span>
                @endif
            </p>
        </div>
    @else
        <div style="text-align: center; padding: 40px 20px; background-color: #f8f9fa; border-radius: 5px;">
            <h4 style="color: #666; margin-bottom: 10px;">Tidak Ada Detail Bahan</h4>
            <p style="color: #888;">Permintaan ini tidak memiliki detail bahan baku.</p>
        </div>
    @endif
</div>

<!-- Action Area -->
@if($permintaan->status == 'menunggu')
<div class="card" style="text-align: center; background-color: #fff3cd; border-color: #ffeaa7;">
    <h5 style="color: #856404; margin-bottom: 15px;">Permintaan Sedang Diproses</h5>
    <p style="color: #856404; margin-bottom: 15px;">Permintaan Anda sedang ditinjau oleh pihak gudang. Anda masih bisa mengedit atau membatalkan permintaan ini.</p>
    <div>
        <a href="{{ route('dapur.permintaan.edit', $permintaan->id) }}" class="btn btn-warning">Edit Permintaan</a>
        <form method="POST" action="{{ route('dapur.permintaan.destroy', $permintaan->id) }}" style="display: inline; margin-left: 10px;">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger" onclick="return confirm('Yakin ingin membatalkan permintaan ini?')">Batalkan Permintaan</button>
        </form>
    </div>
</div>
@elseif($permintaan->status == 'disetujui')
<div class="card" style="text-align: center; background-color: #d4edda; border-color: #c3e6cb;">
    <h5 style="color: #155724; margin-bottom: 15px;">Permintaan Disetujui</h5>
    <p style="color: #155724;">Selamat! Semua bahan yang Anda minta telah tersedia dan permintaan telah disetujui. Anda dapat mulai memasak sesuai jadwal.</p>
</div>
@elseif($permintaan->status == 'ditolak')
<div class="card" style="text-align: center; background-color: #f8d7da; border-color: #f5c6cb;">
    <h5 style="color: #721c24; margin-bottom: 15px;">Permintaan Ditolak</h5>
    <p style="color: #721c24;">Mohon maaf, permintaan Anda tidak dapat dipenuhi karena stok bahan tidak mencukupi. Silakan buat permintaan baru dengan bahan yang berbeda atau kurangi jumlah porsi.</p>
    <a href="{{ route('dapur.permintaan.create') }}" class="btn btn-primary" style="margin-top: 10px;">Buat Permintaan Baru</a>
</div>
@endif
@endsection