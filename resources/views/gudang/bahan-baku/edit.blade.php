@extends('layouts.app')

@section('title', 'Update Stok Bahan Baku')

@section('content')
<div class="card">
    <h2>Update Stok Bahan Baku</h2>
    <p>Update jumlah stok untuk: <strong>{{ $bahanBaku->nama }}</strong></p>
</div>

<div class="card">
    <!-- Info Bahan Baku -->
    <div class="alert alert-info">
        <h4>Informasi Bahan Baku:</h4>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-top: 10px;">
            <div><strong>Nama:</strong> {{ $bahanBaku->nama }}</div>
            <div><strong>Kategori:</strong> {{ $bahanBaku->kategori }}</div>
            <div><strong>Stok Saat Ini:</strong> {{ $bahanBaku->jumlah }} {{ $bahanBaku->satuan }}</div>
            <div><strong>Status:</strong>
                @if($bahanBaku->status == 'tersedia')
                <span class="badge badge-success">Tersedia</span>
                @elseif($bahanBaku->status == 'segera_kadaluarsa')
                <span class="badge badge-warning">Segera Kadaluarsa</span>
                @elseif($bahanBaku->status == 'kadaluarsa')
                <span class="badge badge-danger">Kadaluarsa</span>
                @else
                <span class="badge badge-secondary">Habis</span>
                @endif
            </div>
            <div><strong>Tanggal Masuk:</strong> {{ date('d/m/Y', strtotime($bahanBaku->tanggal_masuk)) }}</div>
            <div><strong>Tanggal Kadaluarsa:</strong> {{ date('d/m/Y', strtotime($bahanBaku->tanggal_kadaluarsa)) }}</div>
        </div>
    </div>

    <form action="{{ route('gudang.bahan-baku.update', $bahanBaku->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="jumlah">Jumlah Stok Baru: <span style="color: red;">*</span></label>
            <div style="display: flex; align-items: center; gap: 10px;">
                <input type="number"
                    id="jumlah"
                    name="jumlah"
                    class="form-control"
                    style="flex: 1;"
                    min="0"
                    step="0.01"
                    required
                    value="{{ old('jumlah', $bahanBaku->jumlah) }}"
                    placeholder="Masukkan jumlah stok baru">
                <span style="font-weight: bold; color: #666;">{{ $bahanBaku->satuan }}</span>
            </div>

            @error('jumlah')
            <small style="color: red;">{{ $message }}</small>
            @enderror

            <small style="color: #666; display: block; margin-top: 5px;">
                Nilai stok tidak boleh kurang dari 0
            </small>
        </div>

        <div class="alert alert-warning">
            <h4>Perhatian:</h4>
            <ul style="margin: 10px 0 0 20px;">
                <li>Perubahan stok akan mempengaruhi status bahan baku secara otomatis</li>
                <li>Jika stok diubah menjadi 0, status akan berubah menjadi "Habis"</li>
                <li>Status akan dihitung ulang berdasarkan tanggal kadaluarsa</li>
            </ul>
        </div>

        <div style="display: flex; gap: 10px; margin-top: 20px;">
            <button type="submit" class="btn btn-success">
                Update Stok
            </button>
            <a href="{{ route('gudang.bahan-baku.index') }}" class="btn btn-secondary">
                Batal
            </a>
        </div>
    </form>
</div>

<script>
    // Real-time validation
    document.getElementById('jumlah').addEventListener('input', function() {
        const value = parseFloat(this.value);
        const submitBtn = document.querySelector('button[type="submit"]');

        if (value < 0 || isNaN(value)) {
            this.style.borderColor = '#e74c3c';
            submitBtn.disabled = true;
            submitBtn.style.opacity = '0.5';
        } else {
            this.style.borderColor = '#27ae60';
            submitBtn.disabled = false;
            submitBtn.style.opacity = '1';
        }
    });
</script>
@endsection