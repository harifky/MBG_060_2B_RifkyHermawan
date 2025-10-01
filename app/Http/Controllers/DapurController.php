<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DapurController extends Controller
{
    public function dashboard()
    {
        $userId = Auth::id();
        
        // Total permintaan user ini
        $totalPermintaan = DB::select("
            SELECT COUNT(*) as total 
            FROM permintaan 
            WHERE pemohon_id = ?
        ", [$userId])[0]->total;
        
        // Permintaan menunggu
        $permintaanMenunggu = DB::select("
            SELECT COUNT(*) as total 
            FROM permintaan 
            WHERE pemohon_id = ? AND status = 'menunggu'
        ", [$userId])[0]->total;
        
        // Permintaan disetujui
        $permintaanDisetujui = DB::select("
            SELECT COUNT(*) as total 
            FROM permintaan 
            WHERE pemohon_id = ? AND status = 'disetujui'
        ", [$userId])[0]->total;
        
        // Permintaan ditolak
        $permintaanDitolak = DB::select("
            SELECT COUNT(*) as total 
            FROM permintaan 
            WHERE pemohon_id = ? AND status = 'ditolak'
        ", [$userId])[0]->total;

        // Permintaan terbaru user ini
        $permintaanTerbaru = DB::select("
            SELECT * FROM permintaan 
            WHERE pemohon_id = ?
            ORDER BY created_at DESC 
            LIMIT 5
        ", [$userId]);

        // Bahan yang tersedia
        $bahanTersedia = DB::select("
            SELECT COUNT(*) as total 
            FROM bahan_baku 
            WHERE status = 'tersedia'
        ")[0]->total;

        return view('dapur.dashboard', compact(
            'totalPermintaan', 'permintaanMenunggu', 'permintaanDisetujui',
            'permintaanDitolak', 'permintaanTerbaru', 'bahanTersedia'
        ));
    }

    // Lihat Bahan Baku yang Tersedia
    public function bahanBaku()
    {
        $bahanBaku = DB::select("
            SELECT * FROM bahan_baku 
            WHERE status = 'tersedia' 
            ORDER BY nama ASC
        ");
        
        return view('dapur.bahan-baku.index', compact('bahanBaku'));
    }

     // Kelola Permintaan
    public function permintaan()
    {
        $userId = Auth::id();
        
        $permintaan = DB::select("
            SELECT * FROM permintaan 
            WHERE pemohon_id = ?
            ORDER BY created_at DESC
        ", [$userId]);
        
        return view('dapur.permintaan.index', compact('permintaan'));
    }
}