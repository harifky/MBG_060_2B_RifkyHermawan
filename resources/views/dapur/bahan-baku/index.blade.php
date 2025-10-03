@extends('layouts.app')

@section('title', 'Daftar Bahan Baku - Dapur')

@section('content')
<div class="card">
    <h2>Daftar Bahan Baku</h2>
    <p>Lihat ketersediaan bahan baku untuk kebutuhan memasak</p>
</div>

<div class="card">
    @if(count($bahanBaku) > 0)
    <table class="table">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Bahan</th>
                <th>Kategori</th>
                <th>Stok</th>
                <th>Satuan</th>
                <th>Status</th>
                <th>Tanggal Kadaluarsa</th>
            </tr>
        </thead>
        <tbody>
            @foreach($bahanBaku as $index => $bahan)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td><strong>{{ $bahan->nama }}</strong></td>
                <td>{{ $bahan->kategori }}</td>
                <td>
                    @if($bahan->jumlah <= 0)
                        <span style="color: #e74c3c; font-weight: bold;">{{ $bahan->jumlah }}</span>
                    @elseif($bahan->jumlah <= 10)
                        <span style="color: #f39c12; font-weight: bold;">{{ $bahan->jumlah }}</span>
                    @else
                        <span style="color: #27ae60; font-weight: bold;">{{ $bahan->jumlah }}</span>
                    @endif
                </td>
                <td>{{ $bahan->satuan }}</td>
                <td>
                    @if($bahan->status == 'tersedia')
                        @if($bahan->jumlah <= 0)
                            <span class="badge bg-danger">
                                <i class="bi bi-x-circle me-1"></i>Habis
                            </span>
                        @elseif($bahan->jumlah <= 10)
                            <span class="badge bg-warning text-dark">
                                <i class="bi bi-exclamation-triangle me-1"></i>Hampir Habis
                            </span>
                        @else
                            <span class="badge bg-success">
                                <i class="bi bi-check-circle me-1"></i>Tersedia
                            </span>
                        @endif
                    @elseif($bahan->status == 'habis')
                        <span class="badge bg-danger">
                            <i class="bi bi-x-circle me-1"></i>Habis
                        </span>
                    @elseif($bahan->status == 'kadaluarsa')
                        <span class="badge bg-dark">
                            <i class="bi bi-calendar-x me-1"></i>Kadaluarsa
                        </span>
                    @endif
                </td>
                <td>
                    @if($bahan->tanggal_kadaluarsa)
                        @php
                            $kadaluarsa = \Carbon\Carbon::parse($bahan->tanggal_kadaluarsa);
                            $today = \Carbon\Carbon::today();
                            $diffDays = $today->diffInDays($kadaluarsa, false);
                        @endphp
                        
                        @if($diffDays < 0)
                            <span style="color: #e74c3c; font-weight: bold;">
                                {{ date('d/m/Y', strtotime($bahan->tanggal_kadaluarsa)) }} 
                                (Kadaluarsa)
                            </span>
                        @elseif($diffDays <= 7)
                            <span style="color: #f39c12; font-weight: bold;">
                                {{ date('d/m/Y', strtotime($bahan->tanggal_kadaluarsa)) }} 
                                ({{ $diffDays }} hari lagi)
                            </span>
                        @else
                            {{ date('d/m/Y', strtotime($bahan->tanggal_kadaluarsa)) }}
                        @endif
                    @else
                        -
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <div style="text-align: center; padding: 60px 20px;">
        <h3 style="color: #666;">Tidak Ada Data Bahan Baku</h3>
        <p style="color: #888; margin: 15px 0;">Belum ada bahan baku yang tersedia di gudang</p>
    </div>
    @endif
</div>

<div class="alert alert-info">
    <h4>Keterangan Status:</h4>
    <ul style="margin: 10px 0 5px 20px; padding: 0; line-height: 1.8;">
        <li style="margin-bottom: 8px;">
            <span class="badge bg-success">
                <i class="bi bi-check-circle me-1"></i>Tersedia
            </span> | Stok lebih dari 10 unit
        </li>
        <li style="margin-bottom: 8px;">
            <span class="badge bg-warning text-dark">
                <i class="bi bi-exclamation-triangle me-1"></i>Hampir Habis
            </span> | Stok 1-10 unit
        </li>
        <li style="margin-bottom: 8px;">
            <span class="badge bg-danger">
                <i class="bi bi-x-circle me-1"></i>Habis
            </span> | Stok 0 atau sudah habis
        </li>
        <li style="margin-bottom: 8px;">
            <span class="badge bg-dark">
                <i class="bi bi-calendar-x me-1"></i>Kadaluarsa
            </span> | Sudah melewati tanggal kadaluarsa
        </li>
    </ul>
    <p style="margin-top: 15px; color: #666;">
        <strong>Tips:</strong> Gunakan informasi ini untuk merencanakan permintaan bahan baku yang tepat. 
        Pastikan bahan yang diminta masih tersedia dan belum mendekati tanggal kadaluarsa.
    </p>
</div>
@endsection