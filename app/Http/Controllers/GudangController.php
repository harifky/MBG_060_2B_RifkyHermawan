<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GudangController extends Controller
{
    public function dashboard()
    {
        // Total bahan baku
        $totalBahan = DB::select("SELECT COUNT(*) as total FROM bahan_baku")[0]->total;
        
        // Bahan tersedia
        $bahanTersedia = DB::select("SELECT COUNT(*) as total FROM bahan_baku WHERE status = 'tersedia'")[0]->total;
        
        // Bahan hampir habis (stok <= 10)
        $bahanHampirHabis = DB::select("SELECT COUNT(*) as total FROM bahan_baku WHERE jumlah <= 10")[0]->total;
        
        // Bahan kadaluarsa
        $bahanKadaluarsa = DB::select("SELECT COUNT(*) as total FROM bahan_baku WHERE status = 'kadaluarsa'")[0]->total;
        
        // Permintaan menunggu
        $permintaanMenunggu = DB::select("SELECT COUNT(*) as total FROM permintaan WHERE status = 'menunggu'")[0]->total;

        // Bahan segera kadaluarsa (3 hari ke depan)
        $bahanSegera = DB::select("
            SELECT * FROM bahan_baku 
            WHERE tanggal_kadaluarsa <= DATE_ADD(CURDATE(), INTERVAL 3 DAY)
            AND tanggal_kadaluarsa > CURDATE()
            AND jumlah > 0
        ");

        // Permintaan terbaru yang menunggu
        $permintaanTerbaru = DB::select("
            SELECT p.*, u.name as pemohon_name, u.email as pemohon_email
            FROM permintaan p
            JOIN user u ON p.pemohon_id = u.id
            WHERE p.status = 'menunggu'
            ORDER BY p.created_at DESC
            LIMIT 5
        ");

        return view('gudang.dashboard', compact(
            'totalBahan', 'bahanTersedia', 'bahanHampirHabis', 
            'bahanKadaluarsa', 'permintaanMenunggu', 'bahanSegera', 
            'permintaanTerbaru'
        ));
    }
}