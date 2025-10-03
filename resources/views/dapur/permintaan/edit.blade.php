@extends('layouts.app')

@section('title', 'Edit Permintaan Bahan - Dapur')

@section('content')
<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2>Edit Permintaan Bahan Baku</h2>
        <a href="{{ route('dapur.permintaan.show', $permintaan->id) }}" class="btn btn-secondary">← Kembali</a>
    </div>
</div>

@if($permintaan->status != 'menunggu')
<div class="alert alert-warning">
    <strong>Peringatan:</strong> Permintaan ini sudah diproses dan tidak dapat diedit lagi.
    <a href="{{ route('dapur.permintaan.show', $permintaan->id) }}">Lihat detail permintaan</a>
</div>
@else

<form method="POST" action="{{ route('dapur.permintaan.update', $permintaan->id) }}">
    @csrf
    @method('PUT')
    
    <!-- Informasi Dasar Permintaan -->
    <div class="card">
        <h4 style="color: #2c3e50; margin-bottom: 20px;">Informasi Permintaan</h4>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
            <div>
                <label for="menu_makan" style="font-weight: bold; margin-bottom: 5px; display: block;">Menu Makan *</label>
                <input type="text" id="menu_makan" name="menu_makan" class="form-control" 
                       placeholder="Contoh: Nasi Gudeg Ayam" 
                       value="{{ old('menu_makan', $permintaan->menu_makan) }}" required>
                @error('menu_makan')
                    <small style="color: #e74c3c;">{{ $message }}</small>
                @enderror
            </div>
            
            <div>
                <label for="jumlah_porsi" style="font-weight: bold; margin-bottom: 5px; display: block;">Jumlah Porsi *</label>
                <input type="number" id="jumlah_porsi" name="jumlah_porsi" class="form-control" 
                       placeholder="Contoh: 50" min="1" 
                       value="{{ old('jumlah_porsi', $permintaan->jumlah_porsi) }}" required>
                @error('jumlah_porsi')
                    <small style="color: #e74c3c;">{{ $message }}</small>
                @enderror
            </div>
        </div>
        
        <div>
            <label for="tgl_masak" style="font-weight: bold; margin-bottom: 5px; display: block;">Tanggal Masak *</label>
            <input type="date" id="tgl_masak" name="tgl_masak" class="form-control" 
                   min="{{ date('Y-m-d') }}" 
                   value="{{ old('tgl_masak', $permintaan->tgl_masak->format('Y-m-d')) }}" required>
            @error('tgl_masak')
                <small style="color: #e74c3c;">{{ $message }}</small>
            @enderror
        </div>
    </div>

    <!-- Daftar Bahan Baku -->
    <div class="card">
        <h4 style="color: #2c3e50; margin-bottom: 20px;">Daftar Bahan yang Dibutuhkan</h4>
        
        <div id="bahan-container">
            @foreach($details as $index => $detail)
            <div class="bahan-item" style="display: grid; grid-template-columns: 2fr 1fr auto; gap: 15px; align-items: end; margin-bottom: 15px; padding: 15px; border: 1px solid #ddd; border-radius: 5px;">
                <div>
                    <label style="font-weight: bold; margin-bottom: 5px; display: block;">Bahan Baku *</label>
                    <select name="bahan_id[]" class="form-control" required>
                        <option value="">Pilih Bahan Baku</option>
                        @foreach($bahanBaku as $bahan)
                        <option value="{{ $bahan->id }}" {{ $detail->bahan_id == $bahan->id ? 'selected' : '' }}>
                            {{ $bahan->nama }} ({{ $bahan->jumlah }} {{ $bahan->satuan }} tersedia)
                        </option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label style="font-weight: bold; margin-bottom: 5px; display: block;">Jumlah Diminta *</label>
                    <input type="number" name="jumlah_diminta[]" class="form-control" 
                           placeholder="0" min="1" value="{{ $detail->jumlah_diminta }}" required>
                </div>
                
                <div>
                    <button type="button" class="btn btn-danger btn-sm" onclick="removeBahan(this)" 
                            @if(count($details) <= 1) style="display: none;" @endif>Hapus</button>
                </div>
            </div>
            @endforeach
        </div>
        
        <button type="button" class="btn btn-success btn-sm" onclick="addBahan()">+ Tambah Bahan</button>
        
        @error('bahan_id')
            <div style="margin-top: 10px;">
                <small style="color: #e74c3c;">{{ $message }}</small>
            </div>
        @enderror
    </div>

    <!-- Submit Button -->
    <div class="card" style="text-align: center;">
        <button type="submit" class="btn btn-primary" style="padding: 10px 30px;">
            Update Permintaan
        </button>
        <a href="{{ route('dapur.permintaan.show', $permintaan->id) }}" class="btn btn-secondary" style="padding: 10px 30px; margin-left: 10px;">
            Batal
        </a>
    </div>
</form>

@endif

<script>
let bahanCount = 1;

function addBahan() {
    bahanCount++;
    const container = document.getElementById('bahan-container');
    const firstItem = container.children[0];
    const newItem = firstItem.cloneNode(true);
    
    // Reset nilai select dan input
    newItem.querySelector('select').value = '';
    newItem.querySelector('input').value = '';
    
    // Tampilkan tombol hapus
    const removeBtn = newItem.querySelector('button');
    removeBtn.style.display = 'block';
    
    container.appendChild(newItem);
    
    // Update tombol hapus untuk semua item jika ada lebih dari 1 item
    if (container.children.length > 1) {
        Array.from(container.children).forEach(item => {
            item.querySelector('button').style.display = 'block';
        });
    }
}

function removeBahan(button) {
    const container = document.getElementById('bahan-container');
    if (container.children.length > 1) {
        button.parentElement.parentElement.remove();
        
        // Sembunyikan tombol hapus jika hanya tersisa 1 item
        if (container.children.length === 1) {
            container.children[0].querySelector('button').style.display = 'none';
        }
    }
}

// Validasi tanggal tidak boleh masa lalu
document.getElementById('tgl_masak').addEventListener('change', function() {
    const selectedDate = new Date(this.value);
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    
    if (selectedDate < today) {
        alert('Tanggal masak tidak boleh di masa lalu!');
        this.value = '';
    }
});
</script>
@endsection