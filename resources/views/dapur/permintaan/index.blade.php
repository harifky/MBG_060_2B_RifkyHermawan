@extends('layouts.app')

@section('title', 'Permintaan Bahan - Dapur')

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Page Header -->
        <div class="card mb-4 shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1">
                        <i class="bi bi-clipboard-check me-2"></i>Permintaan Bahan Baku
                    </h4>
                    <p class="mb-0 text-muted">Kelola permintaan bahan baku untuk kebutuhan memasak</p>
                </div>
                <a href="{{ route('dapur.permintaan.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-2"></i>Ajukan Permintaan
                </a>
            </div>
        </div>

        <!-- Data Table -->
        <div class="card shadow-sm">
            <div class="card-body">
                @if(count($permintaan) > 0)
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th scope="col" class="text-center">#</th>
                                <th scope="col">Menu Makan</th>
                                <th scope="col" class="text-center">Jumlah Porsi</th>
                                <th scope="col" class="text-center">Tanggal Masak</th>
                                <th scope="col" class="text-center">Status</th>
                                <th scope="col" class="text-center">Tanggal Permintaan</th>
                                <th scope="col" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($permintaan as $index => $p)
                            <tr>
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td>
                                    <strong class="text-primary">{{ $p->menu_makan }}</strong>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-info">{{ $p->jumlah_porsi }} porsi</span>
                                </td>
                                <td class="text-center">
                                    <small class="text-muted">
                                        <i class="bi bi-calendar3 me-1"></i>
                                        {{ date('d/m/Y', strtotime($p->tgl_masak)) }}
                                    </small>
                                </td>
                                <td class="text-center">
                                    @if($p->status == 'menunggu')
                                    <span class="badge bg-warning">
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
                                <td class="text-center">
                                    <small class="text-muted">
                                        <i class="bi bi-calendar-plus me-1"></i>
                                        {{ date('d/m/Y', strtotime($p->created_at)) }}
                                    </small>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group-vertical btn-group-sm" role="group">
                                        <a href="{{ route('dapur.permintaan.show', $p->id) }}" 
                                           class="btn btn-outline-info" title="Lihat Detail">
                                            <i class="bi bi-eye me-1"></i>Detail
                                        </a>
                                        @if($p->status == 'menunggu')
                                        <a href="{{ route('dapur.permintaan.edit', $p->id) }}" 
                                           class="btn btn-outline-warning" title="Edit Permintaan">
                                            <i class="bi bi-pencil me-1"></i>Edit
                                        </a>
                                        <button type="button" class="btn btn-outline-danger" 
                                                title="Hapus Permintaan"
                                                onclick="confirmDelete('{{ $p->id }}', '{{ $p->menu_makan }}')">
                                            <i class="bi bi-trash me-1"></i>Hapus
                                        </button>
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
                        <i class="bi bi-clipboard-x display-1 text-muted"></i>
                    </div>
                    <h4 class="text-muted mb-3">Belum Ada Permintaan</h4>
                    <p class="text-muted mb-4">Anda belum membuat permintaan bahan baku</p>
                    <a href="{{ route('dapur.permintaan.create') }}" class="btn btn-primary btn-lg">
                        <i class="bi bi-plus-circle me-2"></i>Buat Permintaan Pertama
                    </a>
                </div>
                @endif
            </div>
        </div>

        <!-- Status Information -->
        <div class="card mt-4 shadow-sm">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-info-circle me-2"></i>Keterangan Status
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <div class="d-flex align-items-center">
                            <span class="badge bg-warning me-3">
                                <i class="bi bi-clock me-1"></i>Menunggu
                            </span>
                            <small class="text-muted">Permintaan sedang diproses gudang</small>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="d-flex align-items-center">
                            <span class="badge bg-success me-3">
                                <i class="bi bi-check-circle me-1"></i>Disetujui
                            </span>
                            <small class="text-muted">Permintaan telah disetujui, bahan tersedia</small>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="d-flex align-items-center">
                            <span class="badge bg-danger me-3">
                                <i class="bi bi-x-circle me-1"></i>Ditolak
                            </span>
                            <small class="text-muted">Permintaan ditolak, stok tidak mencukupi</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Hidden Form for Delete -->
<form id="deleteForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<script>
function confirmDelete(id, menuName) {
    if (confirm(`Yakin ingin menghapus permintaan "${menuName}"?`)) {
        const form = document.getElementById('deleteForm');
        form.action = `/dapur/permintaan/${id}`;
        form.submit();
    }
}
</script>
@endsection