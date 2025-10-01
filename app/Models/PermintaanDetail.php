<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PermintaanDetail extends Model
{
    use HasFactory;

    protected $table = 'permintaan_detail';
    
    protected $fillable = [
        'permintaan_id',
        'bahan_id',
        'jumlah_diminta',
    ];

    protected $casts = [
        'jumlah_diminta' => 'integer',
    ];

    public $timestamps = false; // Tabel detail biasanya tidak perlu timestamps

    // Relasi ke permintaan
    public function permintaan()
    {
        return $this->belongsTo(Permintaan::class, 'permintaan_id');
    }

    // Relasi ke bahan baku
    public function bahanBaku()
    {
        return $this->belongsTo(BahanBaku::class, 'bahan_id');
    }

    // Accessor untuk cek ketersediaan stok
    public function getIsStockAvailableAttribute()
    {
        return $this->bahanBaku->jumlah >= $this->jumlah_diminta;
    }

    // Accessor untuk total yang diminta dalam satuan
    public function getTotalRequestedAttribute()
    {
        return $this->jumlah_diminta . ' ' . $this->bahanBaku->satuan;
    }
}
