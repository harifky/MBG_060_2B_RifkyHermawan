@extends('layouts.app')

@section('title', 'Permintaan Bahan - Dapur')

@section('content')
<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2>Permintaan Bahan Baku</h2>
        <a href="{{ route('dapur.permintaan.create') }}" class="btn btn-primary">+ Ajukan Permintaan</a>
    </div>
    <p>Kelola permintaan bahan baku untuk kebutuhan memasak</p>
</div>

<div class="card">
    @if(count($permintaan) > 0)
    <table class="table">
        <thead>
            <tr>
                <th style="text-align: center;">No</th>
                <th>Menu Makan</th>
                <th style="text-align: center;">Jumlah Porsi</th>
                <th style="text-align: center;">Tanggal Masak</th>
                <th style="text-align: center;">Status</th>
                <th style="text-align: center;">Tanggal Permintaan</th>
                <th style="text-align: center;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($permintaan as $index => $p)
            <tr>
                <td style="text-align: center;">{{ $index + 1 }}</td>
                <td>{{ $p->menu_makan }}</td>
                <td style="text-align: center;">{{ $p->jumlah_porsi }} porsi</td>
                <td style="text-align: center;">{{ date('d/m/Y', strtotime($p->tgl_masak)) }}</td>
                <td style="text-align: center;">
                    @if($p->status == 'menunggu')
                    <span class="badge badge-warning">Menunggu</span>
                    @elseif($p->status == 'disetujui')
                    <span class="badge badge-success">Disetujui</span>
                    @elseif($p->status == 'ditolak')
                    <span class="badge badge-danger">Ditolak</span>
                    @endif
                </td>
                <td style="text-align: center;">{{ date('d/m/Y', strtotime($p->created_at)) }}</td>
                <td style="text-align: center;">
                    <div style="display: flex; flex-direction: column; gap: 5px; align-items: center;">
                        <a href="{{ route('dapur.permintaan.show', $p->id) }}" class="btn btn-info btn-sm" style="min-width: 60px;">Detail</a>
                        @if($p->status == 'menunggu')
                        <a href="{{ route('dapur.permintaan.edit', $p->id) }}" class="btn btn-warning btn-sm" style="min-width: 60px;">Edit</a>
                        <form method="POST" action="{{ route('dapur.permintaan.destroy', $p->id) }}" style="margin: 0;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" style="min-width: 60px;" onclick="return confirm('Yakin ingin menghapus permintaan ini?')">Hapus</button>
                        </form>
                        @endif
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <div style="text-align: center; padding: 60px 20px;">
        <h3 style="color: #666;">Belum Ada Permintaan</h3>
        <p style="color: #888; margin: 15px 0;">Anda belum membuat permintaan bahan baku</p>
        <a href="{{ route('dapur.permintaan.create') }}" class="btn btn-primary">Buat Permintaan Pertama</a>
    </div>
    @endif
</div>

<div class="alert alert-info">
    <h4>Informasi Status:</h4>
    <ul style="margin: 10px 0 5px 20px; padding: 0; line-height: 1.8;">
        <li style="margin-bottom: 8px;"><span class="badge badge-warning">Menunggu</span> | Permintaan sedang diproses gudang</li>
        <li style="margin-bottom: 8px;"><span class="badge badge-success">Disetujui</span> | Permintaan telah disetujui, bahan tersedia</li>
        <li style="margin-bottom: 8px;"><span class="badge badge-danger">Ditolak</span> | Permintaan ditolak, stok tidak mencukupi</li>
    </ul>
</div>
@endsection