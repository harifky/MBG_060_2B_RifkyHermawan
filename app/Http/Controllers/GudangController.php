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
            'totalBahan',
            'bahanTersedia',
            'bahanHampirHabis',
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

    // Update Stok Bahan Baku
    public function editBahan($id)
    {
        $bahanBaku = DB::select("SELECT * FROM bahan_baku WHERE id = ?", [$id]);

        if (empty($bahanBaku)) {
            return redirect()->route('gudang.bahan-baku.index')
                ->with('error', 'Bahan baku tidak ditemukan.');
        }

        $bahanBaku = $bahanBaku[0];
        return view('gudang.bahan-baku.edit', compact('bahanBaku'));
    }

    public function updateBahan(Request $request, $id)
    {
        // Validasi input - hanya jumlah stok yang bisa diubah
        $request->validate([
            'jumlah' => 'required|integer|min:0', // Sistem menolak jika < 0
        ]);

        // Ambil data bahan baku untuk mendapatkan tanggal kadaluarsa
        $bahanBaku = DB::select("SELECT * FROM bahan_baku WHERE id = ?", [$id]);

        if (empty($bahanBaku)) {
            return redirect()->route('gudang.bahan-baku.index')
                ->with('error', 'Bahan baku tidak ditemukan.');
        }

        $bahanBaku = $bahanBaku[0];

        // Hitung status baru berdasarkan stok yang diupdate
        $statusBaru = $this->calculateStatus($request->jumlah, $bahanBaku->tanggal_kadaluarsa);

        // Update jumlah stok dan status
        DB::update("
            UPDATE bahan_baku 
            SET jumlah = ?, status = ?
            WHERE id = ?
        ", [
            $request->jumlah,
            $statusBaru,
            $id
        ]);

        return redirect()->route('gudang.bahan-baku.index')
            ->with('success', 'Stok bahan baku "' . $bahanBaku->nama . '" berhasil diupdate menjadi ' . $request->jumlah . ' ' . $bahanBaku->satuan);
    }

    public function deleteBahan($id)
    {
        // Ambil data bahan baku berdasarkan ID
        $bahan = DB::selectOne("SELECT * FROM bahan_baku WHERE id = ?", [$id]);

        if (!$bahan) {
            return redirect()->route('gudang.bahan-baku.index')->with('error', 'Bahan baku tidak ditemukan');
        }

        // Cek apakah bahan baku berstatus kadaluarsa
        if ($bahan->status !== 'kadaluarsa') {
            return redirect()->route('gudang.bahan-baku.index')->with('error', 'Hanya bahan baku yang kadaluarsa yang dapat dihapus');
        }

        // Hapus bahan baku
        DB::delete("DELETE FROM bahan_baku WHERE id = ?", [$id]);

        return redirect()->route('gudang.bahan-baku.index')->with('success', 'Bahan baku kadaluarsa berhasil dihapus');
    }

    // Fitur Lihat Status Permintaan
    public function statusPermintaan()
    {
        // Ambil semua permintaan dengan detail pemohon
        $permintaan = DB::select("
            SELECT p.*, u.name as pemohon_name, u.email as pemohon_email
            FROM permintaan p
            JOIN user u ON p.pemohon_id = u.id
            ORDER BY p.created_at DESC
        ");

        return view('gudang.permintaan.status', compact('permintaan'));
    }

    public function detailPermintaan($id)
    {
        // Ambil data permintaan berdasarkan ID dengan detail pemohon
        $permintaan = DB::selectOne("
            SELECT p.*, u.name as pemohon_name, u.email as pemohon_email
            FROM permintaan p
            JOIN user u ON p.pemohon_id = u.id
            WHERE p.id = ?
        ", [$id]);

        // Jika permintaan tidak ditemukan
        if (!$permintaan) {
            abort(404, 'Permintaan tidak ditemukan');
        }

        // Ambil detail bahan baku untuk permintaan ini
        $details = DB::select("
            SELECT pd.*, bb.nama as bahan_nama, bb.satuan, bb.jumlah as stok_tersedia
            FROM permintaan_detail pd
            JOIN bahan_baku bb ON pd.bahan_id = bb.id
            WHERE pd.permintaan_id = ?
            ORDER BY bb.nama ASC
        ", [$id]);

        return view('gudang.permintaan.detail', compact('permintaan', 'details'));
    }

    public function approvePermintaan($id)
    {
        // Validasi permintaan masih menunggu
        $permintaan = DB::selectOne("SELECT * FROM permintaan WHERE id = ? AND status = 'menunggu'", [$id]);
        
        if (!$permintaan) {
            return redirect()->back()->with('error', 'Permintaan tidak ditemukan atau sudah diproses');
        }

        try {
            DB::beginTransaction();

            // Cek ketersediaan stok semua bahan
            $details = DB::select("
                SELECT pd.*, bb.nama as bahan_nama, bb.jumlah as stok_tersedia
                FROM permintaan_detail pd
                JOIN bahan_baku bb ON pd.bahan_id = bb.id
                WHERE pd.permintaan_id = ?
            ", [$id]);

            $bahanKurang = [];
            foreach ($details as $detail) {
                if ($detail->stok_tersedia < $detail->jumlah_diminta) {
                    $bahanKurang[] = $detail->bahan_nama . ' (perlu: ' . $detail->jumlah_diminta . ', tersedia: ' . $detail->stok_tersedia . ')';
                }
            }

            if (!empty($bahanKurang)) {
                throw new \Exception('Stok tidak mencukupi untuk bahan: ' . implode(', ', $bahanKurang));
            }

            // Update status permintaan
            DB::update("UPDATE permintaan SET status = 'disetujui' WHERE id = ?", [$id]);

            // Kurangi stok bahan baku
            foreach ($details as $detail) {
                DB::update("
                    UPDATE bahan_baku 
                    SET jumlah = jumlah - ? 
                    WHERE id = ?
                ", [$detail->jumlah_diminta, $detail->bahan_id]);

                // Update status bahan jika habis
                $bahanBaku = DB::selectOne("SELECT * FROM bahan_baku WHERE id = ?", [$detail->bahan_id]);
                if ($bahanBaku && $bahanBaku->jumlah <= 0) {
                    DB::update("UPDATE bahan_baku SET status = 'habis' WHERE id = ?", [$detail->bahan_id]);
                } elseif ($bahanBaku && $bahanBaku->jumlah <= 10) {
                    DB::update("UPDATE bahan_baku SET status = 'hampir_habis' WHERE id = ?", [$detail->bahan_id]);
                }
            }

            DB::commit();

            return redirect()->route('gudang.permintaan.detail', $id)
                           ->with('success', 'Permintaan berhasil disetujui dan stok telah dikurangi');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Gagal menyetujui permintaan: ' . $e->getMessage());
        }
    }

    public function rejectPermintaan($id)
    {
        // Validasi permintaan masih menunggu
        $permintaan = DB::selectOne("SELECT * FROM permintaan WHERE id = ? AND status = 'menunggu'", [$id]);
        
        if (!$permintaan) {
            return redirect()->back()->with('error', 'Permintaan tidak ditemukan atau sudah diproses');
        }

        try {
            // Update status permintaan
            DB::update("UPDATE permintaan SET status = 'ditolak' WHERE id = ?", [$id]);

            return redirect()->route('gudang.permintaan.detail', $id)
                           ->with('success', 'Permintaan telah ditolak');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menolak permintaan: ' . $e->getMessage());
        }
    }
}
