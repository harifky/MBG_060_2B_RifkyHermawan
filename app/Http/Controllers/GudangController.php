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
            'bahanKadaluarsa'
        ));
    }

    // Kelola Bahan Baku
    public function bahanBaku()
    {
        $bahanBaku = DB::select("
            SELECT * FROM bahan_baku 
            ORDER BY created_at DESC
        ");
        
        return view('gudang.bahan-baku.index', compact('bahanBaku'));
    }

    public function createBahan()
    {
        return view('gudang.bahan-baku.create');
    }

    public function storeBahan(Request $request)
    {
        // Validasi input sesuai dokumen
        $request->validate([
            'nama' => 'required|string|max:255',
            'kategori' => 'required|string|max:100',
            'jumlah' => 'required|numeric|min:0',
            'satuan' => 'required|string|max:50',
            'tanggal_masuk' => 'required|date',
            'tanggal_kadaluarsa' => 'required|date|after:tanggal_masuk',
        ]);

        // Hitung status berdasarkan aturan dokumen
        $status = $this->calculateStatus($request->jumlah, $request->tanggal_kadaluarsa);

        // Insert bahan baku baru dengan raw query
        DB::insert("
            INSERT INTO bahan_baku (nama, kategori, jumlah, satuan, tanggal_masuk, tanggal_kadaluarsa, status, created_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
        ", [
            $request->nama,
            $request->kategori,
            $request->jumlah,
            $request->satuan,
            $request->tanggal_masuk,
            $request->tanggal_kadaluarsa,
            $status
        ]);

        return redirect()->route('gudang.bahan-baku.index')
                        ->with('success', 'Bahan baku "' . $request->nama . '" berhasil ditambahkan dengan status: ' . ucfirst(str_replace('_', ' ', $status)));
    }

    // Helper method untuk menghitung status sesuai dokumen
    private function calculateStatus($jumlah, $tanggalKadaluarsa)
    {
        $tanggalKadaluarsa = date('Y-m-d', strtotime($tanggalKadaluarsa));
        $hariIni = date('Y-m-d');
        $tigaHariLagi = date('Y-m-d', strtotime('+3 days'));
        
        // Habis: jika jumlah = 0
        if ($jumlah <= 0) {
            return 'habis';
        }
        
        // Kadaluarsa: jika hari_ini >= tanggal_kadaluarsa
        if ($hariIni >= $tanggalKadaluarsa) {
            return 'kadaluarsa';
        }
        
        // Segera Kadaluarsa: jika tanggal_kadaluarsa <= 3 hari dari sekarang dan stok > 0
        if ($tanggalKadaluarsa <= $tigaHariLagi && $jumlah > 0) {
            return 'segera_kadaluarsa';
        }
        
        // Tersedia: jika stok > 0 dan tidak masuk kondisi di atas
        return 'tersedia';
    }
}