@extends('layouts.app')

@section('title', 'Kelola Bahan Baku')

@section('content')
<div class="card">
    <h2>Kelola Bahan Baku</h2>
    <p>Daftar semua bahan baku yang tersedia di gudang</p>
    <a href="{{ route('gudang.bahan-baku.create') }}" class="btn btn-success">Tambah Bahan Baku</a>
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
                <th>Tanggal Masuk</th>
                <th>Tanggal Kadaluarsa</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($bahanBaku as $index => $bahan)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td><strong>{{ $bahan->nama }}</strong></td>
                <td>{{ $bahan->kategori }}</td>
                <td>{{ number_format($bahan->jumlah, 0) }}</td>
                <td>{{ $bahan->satuan }}</td>
                <td>{{ date('d/m/Y', strtotime($bahan->tanggal_masuk)) }}</td>
                <td>{{ date('d/m/Y', strtotime($bahan->tanggal_kadaluarsa)) }}</td>
                <td>
                    @if($bahan->status == 'tersedia')
                        <span class="badge badge-success">Tersedia</span>
                    @elseif($bahan->status == 'segera_kadaluarsa')
                        <span class="badge badge-warning">Segera Kadaluarsa</span>
                    @elseif($bahan->status == 'kadaluarsa')
                        <span class="badge badge-danger">Kadaluarsa</span>
                    @else
                        <span class="badge badge-secondary">Habis</span>
                    @endif
                </td>
                <td>
                    <a href="{{ route('gudang.bahan-baku.edit', $bahan->id) }}" class="btn btn-primary btn-sm">Update Stok</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    @else
    <div style="text-align: center; padding: 60px 20px;">
        <h3 style="color: #666;">Belum Ada Bahan Baku</h3>
        <p style="color: #888; margin: 15px 0;">Mulai dengan menambah bahan baku pertama Anda</p>
        <a href="{{ route('gudang.bahan-baku.create') }}" class="btn btn-success">Tambah Bahan Baku Pertama</a>
    </div>
    @endif
</div>

<div class="alert alert-info">
    <h4>Penjelasan Status:</h4>
    <ul style="margin: 10px 0 5px 20px; padding: 0; line-height: 1.8;">
        <li style="margin-bottom: 8px;"><span class="badge badge-success">Tersedia</span> | Stok > 0 dan masih dalam masa konsumsi</li>
        <li style="margin-bottom: 8px;"><span class="badge badge-warning">Segera Kadaluarsa</span> | Akan kadaluarsa dalam ≤ 3 hari</li>
        <li style="margin-bottom: 8px;"><span class="badge badge-danger">Kadaluarsa</span> | Sudah melewati tanggal kadaluarsa (bisa dihapus)</li>
        <li style="margin-bottom: 8px;"><span class="badge badge-secondary">Habis</span> | Stok = 0</li>
    </ul>
</div>
@endsection