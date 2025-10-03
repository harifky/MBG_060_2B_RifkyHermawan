@extends('layouts.app')

@section('title', 'Status Permintaan - Gudang')

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
    <h2>Status Permintaan Bahan Baku</h2>
    <p>Daftar semua permintaan bahan baku dari dapur</p>
</div>

<div class="card">
    @if(count($permintaan) > 0)
    <table class="table">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Pemohon</th>
                <th>Menu Makan</th>
                <th>Jumlah Porsi</th>
                <th>Tanggal Masak</th>
                <th>Tanggal Permintaan</th>
                <th>Status</th>
                <th>Detail</th>
            </tr>
        </thead>
        <tbody>
            @foreach($permintaan as $index => $p)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>
                    <strong>{{ $p->pemohon_name }}</strong><br>
                    <small style="color: #666;">{{ $p->pemohon_email }}</small>
                </td>
                <td>{{ $p->menu_makan }}</td>
                <td>{{ $p->jumlah_porsi }} porsi</td>
                <td>{{ date('d/m/Y', strtotime($p->tgl_masak)) }}</td>
                <td>{{ date('d/m/Y', strtotime($p->created_at)) }}</td>
                <td>
                    @if($p->status == 'menunggu')
                    <span class="badge bg-warning text-dark">
                        <i class="bi bi-clock me-1"></i>Menunggu
                    </span>
                    @elseif($p->status == 'disetujui')
                    <span class="badge bg-success">
                        <i class="bi bi-check-circle me-1"></i>Disetujui
                    </span>
                    @elseif($p->status == 'ditolak')
                    <span class="badge bg-danger">
                        <i class="bi bi-x-circle me-1"></i>Ditolak
                    </span>
                    @endif
                </td>
                <td>
                    <a href="{{ route('gudang.permintaan.detail', $p->id) }}" class="btn btn-primary btn-sm">Lihat Detail</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <div style="text-align: center; padding: 60px 20px;">
        <h3 style="color: #666;">Belum Ada Permintaan</h3>
        <p style="color: #888; margin: 15px 0;">Saat ini tidak ada permintaan bahan baku yang perlu ditinjau</p>
    </div>
    @endif
</div>

<div class="alert alert-info">
    <h4>Informasi Status Permintaan:</h4>
    <ul style="margin: 10px 0 5px 20px; padding: 0; line-height: 1.8;">
        <li style="margin-bottom: 8px;">
            <span class="badge bg-warning text-dark">
                <i class="bi bi-clock me-1"></i>Menunggu
            </span> | Permintaan baru yang belum diproses
        </li>
        <li style="margin-bottom: 8px;">
            <span class="badge bg-success">
                <i class="bi bi-check-circle me-1"></i>Disetujui
            </span> | Permintaan telah disetujui gudang
        </li>
        <li style="margin-bottom: 8px;">
            <span class="badge bg-danger">
                <i class="bi bi-x-circle me-1"></i>Ditolak
            </span> | Permintaan tidak dapat dipenuhi
        </li>
    </ul>
</div>

@endsection