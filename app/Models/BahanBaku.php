<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BahanBaku extends Model
{
    use HasFactory;

    protected $table = 'bahan_baku';

    protected $fillable = [
        'nama',
        'kategori',
        'jumlah',
        'satuan',
        'tanggal_masuk',
        'tanggal_kadaluarsa',
        'status',
    ];

    protected $casts = [
        'tanggal_masuk' => 'date',
        'tanggal_kadaluarsa' => 'date',
        'created_at' => 'datetime',
        'jumlah' => 'integer',
    ];

    // Relasi ke detail permintaan
    public function permintaanDetail()
    {
        return $this->hasMany(PermintaanDetail::class, 'bahan_id');
    }

    // Scope untuk status tertentu
    public function scopeTersedia($query)
    {
        return $query->where('status', 'tersedia');
    }

    public function scopeSegera($query)
    {
        return $query->where('status', 'segera_kadaluarsa');
    }

    public function scopeKadaluarsa($query)
    {
        return $query->where('status', 'kadaluarsa');
    }

    public function scopeHabis($query)
    {
        return $query->where('status', 'habis');
    }

    // Accessor untuk cek apakah bahan hampir habis
    public function getIsLowStockAttribute()
    {
        return $this->jumlah <= 10;
    }

    // Accessor untuk cek apakah bahan segera kadaluarsa
    public function getIsExpiringSoonAttribute()
    {
        return $this->tanggal_kadaluarsa <= now()->addDays(7);
    }
}
