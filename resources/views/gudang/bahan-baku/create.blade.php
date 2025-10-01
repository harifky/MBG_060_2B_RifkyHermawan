@extends('layouts.app')

@section('title', 'Tambah Bahan Baku')

@section('content')
<div class="card">
    <h2>Tambah Bahan Baku Baru</h2>
    <p>Lengkapi form di bawah ini untuk menambah bahan baku ke sistem</p>
</div>

<div class="card">
    <form action="{{ route('gudang.bahan-baku.store') }}" method="POST">
        @csrf
        
        <div class="form-group">
            <label for="nama">Nama Bahan Baku: <span style="color: red;">*</span></label>
            <input type="text" 
                   id="nama" 
                   name="nama" 
                   class="form-control" 
                   required 
                   value="{{ old('nama') }}"
                   placeholder="Contoh: Beras Premium, Daging Ayam, dll">
            @error('nama')
                <small style="color: red;">{{ $message }}</small>
            @enderror
        </div>
        
        <div class="form-group">
            <label for="kategori">Kategori: <span style="color: red;">*</span></label>
            <select id="kategori" name="kategori" class="form-control" required>
                <option value="">-- Pilih Kategori --</option>
                <option value="Beras & Karbohidrat" {{ old('kategori') == 'Beras & Karbohidrat' ? 'selected' : '' }}>Beras & Karbohidrat</option>
                <option value="Daging & Protein" {{ old('kategori') == 'Daging & Protein' ? 'selected' : '' }}>Daging & Protein</option>
                <option value="Sayuran" {{ old('kategori') == 'Sayuran' ? 'selected' : '' }}>Sayuran</option>
                <option value="Bumbu & Rempah" {{ old('kategori') == 'Bumbu & Rempah' ? 'selected' : '' }}>Bumbu & Rempah</option>
                <option value="Minyak & Lemak" {{ old('kategori') == 'Minyak & Lemak' ? 'selected' : '' }}>Minyak & Lemak</option>
                <option value="Susu & Olahan" {{ old('kategori') == 'Susu & Olahan' ? 'selected' : '' }}>Susu & Olahan</option>
                <option value="Lainnya" {{ old('kategori') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
            </select>
            @error('kategori')
                <small style="color: red;">{{ $message }}</small>
            @enderror
        </div>
        
        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 15px;">
            <div class="form-group">
                <label for="jumlah">Jumlah Stok: <span style="color: red;">*</span></label>
                <input type="number" 
                       id="jumlah" 
                       name="jumlah" 
                       class="form-control" 
                       min="0" 
                       step="0.01"
                       required 
                       value="{{ old('jumlah') }}"
                       placeholder="0">
                @error('jumlah')
                    <small style="color: red;">{{ $message }}</small>
                @enderror
            </div>
            
            <div class="form-group">
                <label for="satuan">Satuan: <span style="color: red;">*</span></label>
                <select id="satuan" name="satuan" class="form-control" required>
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
                    <small style="color: red;">{{ $message }}</small>
                @enderror
            </div>
        </div>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
            <div class="form-group">
                <label for="tanggal_masuk">Tanggal Masuk: <span style="color: red;">*</span></label>
                <input type="date" 
                       id="tanggal_masuk" 
                       name="tanggal_masuk" 
                       class="form-control" 
                       required 
                       value="{{ old('tanggal_masuk', date('Y-m-d')) }}">
                @error('tanggal_masuk')
                    <small style="color: red;">{{ $message }}</small>
                @enderror
            </div>
            
            <div class="form-group">
                <label for="tanggal_kadaluarsa">Tanggal Kadaluarsa: <span style="color: red;">*</span></label>
                <input type="date" 
                       id="tanggal_kadaluarsa" 
                       name="tanggal_kadaluarsa" 
                       class="form-control" 
                       required 
                       value="{{ old('tanggal_kadaluarsa') }}"
                       min="{{ date('Y-m-d', strtotime('+1 day')) }}">
                @error('tanggal_kadaluarsa')
                    <small style="color: red;">{{ $message }}</small>
                @enderror
            </div>
        </div>

        <div class="alert alert-info">
            <h4>Informasi Status Bahan:</h4>
            <ul style="margin: 10px 0 5px 20px; padding: 0; line-height: 1.8;">
                <li><strong>Tersedia |</strong> Stok > 0 dan belum kadaluarsa</li>
                <li><strong>Segera Kadaluarsa |</strong> Kadaluarsa ≤ 3 hari dari sekarang</li>
                <li><strong>Kadaluarsa |</strong> Sudah melewati tanggal kadaluarsa</li>
                <li><strong>Habis |</strong> Jumlah stok = 0</li>
            </ul>
            <p><em>Status akan dihitung otomatis oleh sistem.</em></p>
        </div>
        
        <div style="display: flex; gap: 10px; margin-top: 20px;">
            <button type="submit" class="btn btn-success">
                Simpan Bahan Baku
            </button>
            <a href="{{ route('gudang.bahan-baku.index') }}" class="btn btn-secondary">
                Batal
            </a>
        </div>
    </form>
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