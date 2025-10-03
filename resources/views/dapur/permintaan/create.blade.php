@extends('layouts.app')

@section('title', 'Buat Permintaan Bahan - Dapur')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Page Header -->
            <div class="card mb-4 shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-1">
                            <i class="bi bi-plus-circle me-2"></i>Buat Permintaan Bahan Baku
                        </h4>
                        <p class="mb-0 text-muted">Lengkapi form berikut untuk mengajukan permintaan bahan baku</p>
                    </div>
                    <a href="{{ route('dapur.permintaan.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-2"></i>Kembali
                    </a>
                </div>
            </div>

            @if(count($bahanBaku) > 0)
            <!-- Panel Bahan Baku Tersedia -->
            <div class="card mb-4 shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-check-circle me-2"></i>Bahan Baku Tersedia ({{ count($bahanBaku) }} jenis)
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead class="table-success">
                                <tr>
                                    <th>Nama Bahan</th>
                                    <th>Kategori</th>
                                    <th>Stok Tersedia</th>
                                    <th>Tanggal Kadaluarsa</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($bahanBaku as $bahan)
                                <tr>
                                    <td><strong>{{ $bahan->nama }}</strong></td>
                                    <td>{{ $bahan->kategori }}</td>
                                    <td>{{ $bahan->jumlah }} {{ $bahan->satuan }}</td>
                                    <td>{{ date('d/m/Y', strtotime($bahan->tanggal_kadaluarsa)) }}</td>
                                    <td>
                                        @if($bahan->status == 'tersedia')
                                            <span class="badge bg-success">Tersedia</span>
                                        @elseif($bahan->status == 'segera_kadaluarsa')
                                            <span class="badge bg-warning text-dark">Segera Kadaluarsa</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Form Permintaan -->
            <form method="POST" action="{{ route('dapur.permintaan.store') }}">
                @csrf
                
                <!-- Informasi Dasar -->
                <div class="card mb-4 shadow-sm">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="bi bi-info-circle me-2"></i>Informasi Permintaan
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="menu_makan" class="form-label">
                                    Menu Makan <span class="text-danger">*</span>
                                </label>
                                <input type="text" id="menu_makan" name="menu_makan" 
                                       class="form-control @error('menu_makan') is-invalid @enderror" 
                                       placeholder="Contoh: Nasi Gudeg Ayam" 
                                       value="{{ old('menu_makan') }}" required>
                                @error('menu_makan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="jumlah_porsi" class="form-label">
                                    Jumlah Porsi <span class="text-danger">*</span>
                                </label>
                                <input type="number" id="jumlah_porsi" name="jumlah_porsi" 
                                       class="form-control @error('jumlah_porsi') is-invalid @enderror" 
                                       placeholder="50" min="1" 
                                       value="{{ old('jumlah_porsi') }}" required>
                                @error('jumlah_porsi')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="tgl_masak" class="form-label">
                                    Tanggal Masak <span class="text-danger">*</span>
                                </label>
                                <input type="date" id="tgl_masak" name="tgl_masak" 
                                       class="form-control @error('tgl_masak') is-invalid @enderror" 
                                       min="{{ date('Y-m-d') }}" 
                                       value="{{ old('tgl_masak') }}" required>
                                @error('tgl_masak')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Daftar Bahan -->
                <div class="card mb-4 shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="bi bi-list-check me-2"></i>Daftar Bahan yang Dibutuhkan
                        </h5>
                        <button type="button" class="btn btn-success btn-sm" onclick="addBahan()">
                            <i class="bi bi-plus me-2"></i>Tambah Bahan
                        </button>
                    </div>
                    <div class="card-body">
                        <div id="bahan-container">
                            <!-- Item Bahan Template -->
                            <div class="bahan-item border rounded p-3 mb-3">
                                <div class="row align-items-end">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">
                                            Bahan Baku <span class="text-danger">*</span>
                                        </label>
                                        <select name="bahan_id[]" class="form-select" required onchange="updateSatuan(this)">
                                            <option value="">Pilih Bahan Baku</option>
                                            @foreach($bahanBaku as $bahan)
                                            <option value="{{ $bahan->id }}" 
                                                    data-satuan="{{ $bahan->satuan }}"
                                                    data-stok="{{ $bahan->jumlah }}">
                                                {{ $bahan->nama }} - {{ $bahan->kategori }} (Stok: {{ $bahan->jumlah }} {{ $bahan->satuan }})
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">
                                            Jumlah Diminta <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group">
                                            <input type="number" name="jumlah_diminta[]" class="form-control" 
                                                   min="0.1" step="0.1" required placeholder="0">
                                            <span class="input-group-text satuan-text">-</span>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-2 mb-3">
                                        <button type="button" class="btn btn-outline-danger btn-sm w-100" 
                                                onclick="removeBahan(this)" style="display: none;">
                                            <i class="bi bi-trash"></i> Hapus
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-end">
                            <a href="{{ route('dapur.permintaan.index') }}" class="btn btn-outline-secondary btn-lg me-3">
                                <i class="bi bi-x-circle me-2"></i>Batal
                            </a>
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-send me-2"></i>Ajukan Permintaan
                            </button>
                        </div>
                    </div>
                </div>
            </form>
            
            @else
            <!-- Tidak Ada Bahan Tersedia -->
            <div class="card shadow-sm">
                <div class="card-body text-center py-5">
                    <div class="mb-4">
                        <i class="bi bi-exclamation-triangle display-1 text-warning"></i>
                    </div>
                    <h4 class="text-warning mb-3">Tidak Ada Bahan Baku Tersedia</h4>
                    <p class="text-muted mb-4">
                        Saat ini tidak ada bahan baku yang tersedia untuk diminta.<br>
                        Silakan hubungi pihak gudang atau coba lagi nanti.
                    </p>
                    <a href="{{ route('dapur.permintaan.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-2"></i>Kembali
                    </a>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<script>
let bahanCount = 1;

function addBahan() {
    bahanCount++;
    const container = document.getElementById('bahan-container');
    const newItem = container.children[0].cloneNode(true);
    
    // Reset nilai
    newItem.querySelector('select').value = '';
    newItem.querySelector('input[name="jumlah_diminta[]"]').value = '';
    newItem.querySelector('.satuan-text').textContent = '-';
    
    // Tampilkan tombol hapus
    newItem.querySelector('button').style.display = 'block';
    
    container.appendChild(newItem);
    
    // Update event listener untuk select yang baru
    const newSelect = newItem.querySelector('select');
    newSelect.addEventListener('change', function() {
        updateSatuan(this);
    });
    
    // Tampilkan tombol hapus untuk item pertama jika ada lebih dari 1 item
    if (container.children.length > 1) {
        container.children[0].querySelector('button').style.display = 'block';
    }
}

function removeBahan(button) {
    const container = document.getElementById('bahan-container');
    if (container.children.length > 1) {
        button.closest('.bahan-item').remove();
        
        // Sembunyikan tombol hapus jika hanya tersisa 1 item
        if (container.children.length === 1) {
            container.children[0].querySelector('button').style.display = 'none';
        }
    }
}

function updateSatuan(selectElement) {
    const satuanText = selectElement.closest('.bahan-item').querySelector('.satuan-text');
    
    if (selectElement.value) {
        const selectedOption = selectElement.options[selectElement.selectedIndex];
        const satuan = selectedOption.getAttribute('data-satuan');
        satuanText.textContent = satuan || '-';
    } else {
        satuanText.textContent = '-';
    }
}

// Initialize event listeners
document.addEventListener('DOMContentLoaded', function() {
    // Event listener untuk select pertama
    const firstSelect = document.querySelector('select[name="bahan_id[]"]');
    if (firstSelect) {
        firstSelect.addEventListener('change', function() {
            updateSatuan(this);
        });
    }
    
    // Validasi tanggal tidak boleh masa lalu
    const tglMasak = document.getElementById('tgl_masak');
    if (tglMasak) {
        tglMasak.addEventListener('change', function() {
            const selectedDate = new Date(this.value);
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            
            if (selectedDate < today) {
                alert('Tanggal masak tidak boleh di masa lalu!');
                this.value = '';
            }
        });
    }
});
</script>
@endsection
