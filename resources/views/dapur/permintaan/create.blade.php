@extends('layouts.app')

@section('title', 'Buat Permintaan Bahan - Dapur')

@section('content')
<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2>Buat Permintaan Bahan Baku</h2>
        <a href="{{ route('dapur.permintaan.index') }}" class="btn btn-secondary">← Kembali</a>
    </div>
</div>

<form method="POST" action="{{ route('dapur.permintaan.store') }}">
    @csrf
    
    <!-- Informasi Dasar Permintaan -->
    <div class="card">
        <h4 style="color: #2c3e50; margin-bottom: 20px;">Informasi Permintaan</h4>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
            <div>
                <label for="menu_makan" style="font-weight: bold; margin-bottom: 5px; display: block;">Menu Makan *</label>
                <input type="text" id="menu_makan" name="menu_makan" class="form-control" 
                       placeholder="Contoh: Nasi Gudeg Ayam" 
                       value="{{ old('menu_makan') }}" required>
                @error('menu_makan')
                    <small style="color: #e74c3c;">{{ $message }}</small>
                @enderror
            </div>
            
            <div>
                <label for="jumlah_porsi" style="font-weight: bold; margin-bottom: 5px; display: block;">Jumlah Porsi *</label>
                <input type="number" id="jumlah_porsi" name="jumlah_porsi" class="form-control" 
                       placeholder="Contoh: 50" min="1" 
                       value="{{ old('jumlah_porsi') }}" required>
                @error('jumlah_porsi')
                    <small style="color: #e74c3c;">{{ $message }}</small>
                @enderror
            </div>
        </div>
        
        <div>
            <label for="tgl_masak" style="font-weight: bold; margin-bottom: 5px; display: block;">Tanggal Masak *</label>
            <input type="date" id="tgl_masak" name="tgl_masak" class="form-control" 
                   min="{{ date('Y-m-d') }}" 
                   value="{{ old('tgl_masak') }}" required>
            @error('tgl_masak')
                <small style="color: #e74c3c;">{{ $message }}</small>
            @enderror
        </div>
    </div>

    <!-- Daftar Bahan Baku -->
    <div class="card">
        <h4 style="color: #2c3e50; margin-bottom: 20px;">Daftar Bahan yang Dibutuhkan</h4>
        
        <div id="bahan-container">
            <div class="bahan-item" style="display: grid; grid-template-columns: 2fr 1fr auto; gap: 15px; align-items: end; margin-bottom: 15px; padding: 15px; border: 1px solid #ddd; border-radius: 5px;">
                <div>
                    <label style="font-weight: bold; margin-bottom: 5px; display: block;">Bahan Baku *</label>
                    <select name="bahan_id[]" class="form-control" required onchange="validateBahanKadaluarsa()">
                        <option value="">Pilih Bahan Baku</option>
                        @foreach($bahanBaku as $bahan)
                        <option value="{{ $bahan->id }}" data-kadaluarsa="{{ $bahan->tanggal_kadaluarsa }}" data-nama="{{ $bahan->nama }}">
                            {{ $bahan->nama }} ({{ $bahan->jumlah }} {{ $bahan->satuan }} tersedia - exp: {{ date('d/m/Y', strtotime($bahan->tanggal_kadaluarsa)) }})
                        </option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label style="font-weight: bold; margin-bottom: 5px; display: block;">Jumlah Diminta *</label>
                    <input type="number" name="jumlah_diminta[]" class="form-control" 
                           placeholder="0" min="1" required>
                </div>
                
                <div>
                    <button type="button" class="btn btn-danger btn-sm" onclick="removeBahan(this)" style="display: none;">Hapus</button>
                </div>
            </div>
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
            Ajukan Permintaan
        </button>
        <a href="{{ route('dapur.permintaan.index') }}" class="btn btn-secondary" style="padding: 10px 30px; margin-left: 10px;">
            Batal
        </a>
    </div>
</form>

<script>
let bahanCount = 1;

function addBahan() {
    bahanCount++;
    const container = document.getElementById('bahan-container');
    const newItem = container.children[0].cloneNode(true);
    
    // Reset nilai select dan input
    const selectElement = newItem.querySelector('select');
    selectElement.value = '';
    selectElement.onchange = validateBahanKadaluarsa; // Add event listener
    newItem.querySelector('input').value = '';
    
    // Tampilkan tombol hapus
    const removeBtn = newItem.querySelector('button');
    removeBtn.style.display = 'block';
    
    container.appendChild(newItem);
    
    // Update tombol hapus untuk item pertama jika ada lebih dari 1 item
    if (container.children.length > 1) {
        container.children[0].querySelector('button').style.display = 'block';
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
        return;
    }
    
    // Validasi tanggal kadaluarsa bahan baku
    validateBahanKadaluarsa();
});

// Fungsi validasi bahan kadaluarsa
function validateBahanKadaluarsa() {
    const tglMasak = document.getElementById('tgl_masak').value;
    if (!tglMasak) return;
    
    const selects = document.querySelectorAll('select[name="bahan_id[]"]');
    const masukDate = new Date(tglMasak);
    let hasKadaluarsa = false;
    let bahanKadaluarsa = [];
    
    selects.forEach(select => {
        if (select.value) {
            const selectedOption = select.options[select.selectedIndex];
            const tanggalKadaluarsa = selectedOption.getAttribute('data-kadaluarsa');
            const namaBahan = selectedOption.getAttribute('data-nama');
            
            if (tanggalKadaluarsa) {
                const kadaluarsaDate = new Date(tanggalKadaluarsa);
                
                if (kadaluarsaDate < masukDate) {
                    hasKadaluarsa = true;
                    bahanKadaluarsa.push(namaBahan + ' (exp: ' + formatDate(kadaluarsaDate) + ')');
                    
                    // Highlight select yang bermasalah
                    select.style.borderColor = '#e74c3c';
                    select.style.backgroundColor = '#fdf2f2';
                } else {
                    // Reset styling jika ok
                    select.style.borderColor = '';
                    select.style.backgroundColor = '';
                }
            }
        }
    });
    
    // Tampilkan warning jika ada bahan kadaluarsa
    let warningDiv = document.getElementById('kadaluarsa-warning');
    if (hasKadaluarsa) {
        if (!warningDiv) {
            warningDiv = document.createElement('div');
            warningDiv.id = 'kadaluarsa-warning';
            warningDiv.style.cssText = 'background-color: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 12px; border-radius: 5px; margin-top: 15px;';
            document.querySelector('form').appendChild(warningDiv);
        }
        warningDiv.innerHTML = '<strong>⚠️ Peringatan:</strong> Bahan berikut akan kadaluarsa sebelum tanggal masak:<br>' + 
                              bahanKadaluarsa.map(b => '• ' + b).join('<br>');
    } else if (warningDiv) {
        warningDiv.remove();
    }
}

// Helper function untuk format tanggal
function formatDate(date) {
    const dd = String(date.getDate()).padStart(2, '0');
    const mm = String(date.getMonth() + 1).padStart(2, '0');
    const yyyy = date.getFullYear();
    return dd + '/' + mm + '/' + yyyy;
}
</script>
@endsection