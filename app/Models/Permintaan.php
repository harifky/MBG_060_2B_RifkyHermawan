<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permintaan extends Model
{
    use HasFactory;

    protected $table = 'permintaan';
    
    protected $fillable = [
        'pemohon_id',
        'tgl_masak',
        'menu_makan',
        'jumlah_porsi',
        'status',
    ];

    protected $casts = [
        'tgl_masak' => 'date',
        'created_at' => 'datetime',
        'jumlah_porsi' => 'integer',
    ];

    // Relasi ke user (pemohon)
    public function pemohon()
    {
        return $this->belongsTo(User::class, 'pemohon_id');
    }

    // Relasi ke detail permintaan
    public function details()
    {
        return $this->hasMany(PermintaanDetail::class, 'permintaan_id');
    }

    // Scope untuk status tertentu
    public function scopeMenunggu($query)
    {
        return $query->where('status', 'menunggu');
    }

    public function scopeDisetujui($query)
    {
        return $query->where('status', 'disetujui');
    }

    public function scopeDitolak($query)
    {
        return $query->where('status', 'ditolak');
    }

    // Method untuk approve permintaan
    public function approve()
    {
        $this->update(['status' => 'disetujui']);
        
        // Kurangi stok bahan baku
        foreach ($this->details as $detail) {
            $bahanBaku = $detail->bahanBaku;
            $bahanBaku->decrement('jumlah', $detail->jumlah_diminta);
            
            // Update status jika habis
            if ($bahanBaku->jumlah <= 0) {
                $bahanBaku->update(['status' => 'habis']);
            }
        }
    }

    // Method untuk reject permintaan
    public function reject()
    {
        $this->update(['status' => 'ditolak']);
    }
}
