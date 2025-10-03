@extends('layouts.app')

@section('title', 'Tambah Bahan Baku')

@section('content')
<div class="row">
    <div class="col-lg-8 mx-auto">
        <!-- Page Header -->
        <div class="card mb-4 shadow-sm">
            <div class="card-header">
                <h4 class="mb-1">
                    <i class="bi bi-plus-circle me-2"></i>Tambah Bahan Baku Baru
                </h4>
                <p class="mb-0 text-muted">Lengkapi form di bawah ini untuk menambah bahan baku ke sistem</p>
            </div>
        </div>

        <!-- Form Card -->
        <div class="card shadow-sm">
            <div class="card-body">
                <form action="{{ route('gudang.bahan-baku.store') }}" method="POST">
                    @csrf
                    
                    <!-- Nama Bahan Baku -->
                    <div class="mb-3">
                        <label for="nama" class="form-label">
                            <i class="bi bi-tag me-1"></i>Nama Bahan Baku 
                            <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               id="nama" 
                               name="nama" 
                               class="form-control @error('nama') is-invalid @enderror" 
                               required 
                               value="{{ old('nama') }}"
                               placeholder="Contoh: Beras Premium, Daging Ayam, dll">
                        @error('nama')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <!-- Kategori -->
                    <div class="mb-3">
                        <label for="kategori" class="form-label">
                            <i class="bi bi-grid-3x3-gap me-1"></i>Kategori 
                            <span class="text-danger">*</span>
                        </label>
                        <select id="kategori" name="kategori" class="form-select @error('kategori') is-invalid @enderror" required>
                            <option value="">-- Pilih Kategori --</option>
                            <option value="Beras & Karbohidrat" {{ old('kategori') == 'Beras & Karbohidrat' ? 'selected' : '' }}>🌾 Beras & Karbohidrat</option>
                            <option value="Daging & Protein" {{ old('kategori') == 'Daging & Protein' ? 'selected' : '' }}>🥩 Daging & Protein</option>
                            <option value="Sayuran" {{ old('kategori') == 'Sayuran' ? 'selected' : '' }}>🥬 Sayuran</option>
                            <option value="Bumbu & Rempah" {{ old('kategori') == 'Bumbu & Rempah' ? 'selected' : '' }}>🌶️ Bumbu & Rempah</option>
                            <option value="Minyak & Lemak" {{ old('kategori') == 'Minyak & Lemak' ? 'selected' : '' }}>🫒 Minyak & Lemak</option>
                            <option value="Susu & Olahan" {{ old('kategori') == 'Susu & Olahan' ? 'selected' : '' }}>🥛 Susu & Olahan</option>
                            <option value="Lainnya" {{ old('kategori') == 'Lainnya' ? 'selected' : '' }}>📦 Lainnya</option>
                        </select>
                        @error('kategori')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <!-- Jumlah dan Satuan -->
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label for="jumlah" class="form-label">
                                <i class="bi bi-123 me-1"></i>Jumlah Stok 
                                <span class="text-danger">*</span>
                            </label>
                            <input type="number" 
                                   id="jumlah" 
                                   name="jumlah" 
                                   class="form-control @error('jumlah') is-invalid @enderror" 
                                   min="0" 
                                   step="0.01"
                                   required 
                                   value="{{ old('jumlah') }}"
                                   placeholder="0">
                            @error('jumlah')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="satuan" class="form-label">
                                <i class="bi bi-rulers me-1"></i>Satuan 
                                <span class="text-danger">*</span>
                            </label>
                            <select id="satuan" name="satuan" class="form-select @error('satuan') is-invalid @enderror" required>
                                <option value="">-- Pilih --</option>
                                <option value="kg" {{ old('satuan') == 'kg' ? 'selected' : '' }}>kg (kilogram)</option>
                                <option value="gram" {{ old('satuan') == 'gram' ? 'selected' : '' }}>gram</option>
                                <option value="liter" {{ old('satuan') == 'liter' ? 'selected' : '' }}>liter</option>
                                <option value="ml" {{ old('satuan') == 'ml' ? 'selected' : '' }}>ml (mililiter)</option>
                                <option value="pcs" {{ old('satuan') == 'pcs' ? 'selected' : '' }}>pcs (pieces)</option>
                                <option value="pack" {{ old('satuan') == 'pack' ? 'selected' : '' }}>pack</option>
                                <option value="karton" {{ old('satuan') == 'karton' ? 'selected' : '' }}>karton</option>
                            </select>
                            @error('satuan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <!-- Tanggal -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="tanggal_masuk" class="form-label">
                                <i class="bi bi-calendar-plus me-1"></i>Tanggal Masuk 
                                <span class="text-danger">*</span>
                            </label>
                            <input type="date" 
                                   id="tanggal_masuk" 
                                   name="tanggal_masuk" 
                                   class="form-control @error('tanggal_masuk') is-invalid @enderror" 
                                   required 
                                   value="{{ old('tanggal_masuk', date('Y-m-d')) }}">
                            @error('tanggal_masuk')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="tanggal_kadaluarsa" class="form-label">
                                <i class="bi bi-calendar-x me-1"></i>Tanggal Kadaluarsa 
                                <span class="text-danger">*</span>
                            </label>
                            <input type="date" 
                                   id="tanggal_kadaluarsa" 
                                   name="tanggal_kadaluarsa" 
                                   class="form-control @error('tanggal_kadaluarsa') is-invalid @enderror" 
                                   required 
                                   value="{{ old('tanggal_kadaluarsa') }}"
                                   min="{{ date('Y-m-d', strtotime('+1 day')) }}">
                            @error('tanggal_kadaluarsa')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Info Panel -->
                    <div class="alert alert-info mb-4">
                        <h6 class="alert-heading">
                            <i class="bi bi-info-circle me-2"></i>Informasi Status Bahan
                        </h6>
                        <div class="row small">
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <span class="badge bg-success me-2">Tersedia</span>
                                    Stok > 0 dan belum kadaluarsa
                                </div>
                                <div class="mb-2">
                                    <span class="badge bg-warning me-2">Segera Kadaluarsa</span>
                                    Kadaluarsa ≤ 3 hari
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <span class="badge bg-danger me-2">Kadaluarsa</span>
                                    Sudah melewati tanggal kadaluarsa
                                </div>
                                <div class="mb-2">
                                    <span class="badge bg-secondary me-2">Habis</span>
                                    Jumlah stok = 0
                                </div>
                            </div>
                        </div>
                        <hr class="my-3">
                        <p class="mb-0 small text-muted">
                            <i class="bi bi-gear me-1"></i>
                            Status akan dihitung otomatis oleh sistem.
                        </p>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="d-flex gap-2 justify-content-end">
                        <a href="{{ route('gudang.bahan-baku.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x-circle me-2"></i>Batal
                        </a>
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-check-circle me-2"></i>Simpan Bahan Baku
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Validasi tanggal kadaluarsa harus setelah tanggal masuk
document.getElementById('tanggal_masuk').addEventListener('change', function() {
    const tanggalMasuk = new Date(this.value);
    const tanggalKadaluarsa = document.getElementById('tanggal_kadaluarsa');
    
    // Set minimum tanggal kadaluarsa = tanggal masuk + 1 hari
    const minDate = new Date(tanggalMasuk);
    minDate.setDate(minDate.getDate() + 1);
    tanggalKadaluarsa.min = minDate.toISOString().split('T')[0];
});

// Auto update kategori berdasarkan nama
document.getElementById('nama').addEventListener('blur', function() {
    const nama = this.value.toLowerCase();
    const kategoriSelect = document.getElementById('kategori');
    
    if (kategoriSelect.value === '') {
        if (nama.includes('beras') || nama.includes('nasi') || nama.includes('mie')) {
            kategoriSelect.value = 'Beras & Karbohidrat';
        } else if (nama.includes('ayam') || nama.includes('daging') || nama.includes('ikan')) {
            kategoriSelect.value = 'Daging & Protein';
        } else if (nama.includes('sayur') || nama.includes('tomat') || nama.includes('bawang')) {
            kategoriSelect.value = 'Sayuran';
        } else if (nama.includes('garam') || nama.includes('merica') || nama.includes('bumbu')) {
            kategoriSelect.value = 'Bumbu & Rempah';
        } else if (nama.includes('minyak') || nama.includes('mentega')) {
            kategoriSelect.value = 'Minyak & Lemak';
        } else if (nama.includes('susu') || nama.includes('keju')) {
            kategoriSelect.value = 'Susu & Olahan';
        }
    }
});
</script>
@endsection