<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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

    public function createPermintaan()
    {
        // Ambil hanya bahan baku yang tersedia (stok > 0 dan tidak kadaluarsa)
        $bahanBaku = DB::select("
            SELECT * FROM bahan_baku 
            WHERE jumlah > 0 
            AND status != 'kadaluarsa'
            ORDER BY nama ASC
        ");
        
        return view('dapur.permintaan.create', compact('bahanBaku'));
    }

    public function storePermintaan(Request $request)
    {
        $request->validate([
            'menu_makan' => 'required|string|max:255',
            'jumlah_porsi' => 'required|integer|min:1',
            'tgl_masak' => 'required|date|after_or_equal:today',
            'bahan_id' => 'required|array|min:1',
            'bahan_id.*' => 'required|integer|exists:bahan_baku,id',
            'jumlah_diminta' => 'required|array|min:1',
            'jumlah_diminta.*' => 'required|integer|min:1',
        ], [
            'menu_makan.required' => 'Menu makan wajib diisi',
            'jumlah_porsi.required' => 'Jumlah porsi wajib diisi',
            'jumlah_porsi.min' => 'Jumlah porsi minimal 1',
            'tgl_masak.required' => 'Tanggal masak wajib diisi',
            'tgl_masak.after_or_equal' => 'Tanggal masak tidak boleh di masa lalu',
            'bahan_id.required' => 'Minimal pilih 1 bahan baku',
            'jumlah_diminta.required' => 'Jumlah diminta wajib diisi',
            'jumlah_diminta.*.min' => 'Jumlah diminta minimal 1',
        ]);

        try {
            DB::beginTransaction();
            
            // Validasi tanggal kadaluarsa bahan baku
            $tglMasak = $request->tgl_masak;
            $bahanIds = $request->bahan_id;
            
            $bahanKadaluarsa = DB::select("
                SELECT nama, tanggal_kadaluarsa 
                FROM bahan_baku 
                WHERE id IN (" . implode(',', array_fill(0, count($bahanIds), '?')) . ") 
                AND tanggal_kadaluarsa < ?
            ", array_merge($bahanIds, [$tglMasak]));
            
            if (!empty($bahanKadaluarsa)) {
                $namaKadaluarsa = array_column($bahanKadaluarsa, 'nama');
                throw new \Exception('Bahan berikut akan kadaluarsa sebelum tanggal masak: ' . implode(', ', $namaKadaluarsa));
            }
            
            // Insert ke tabel permintaan
            $permintaanId = DB::table('permintaan')->insertGetId([
                'pemohon_id' => Auth::id(),
                'menu_makan' => $request->menu_makan,
                'jumlah_porsi' => $request->jumlah_porsi,
                'tgl_masak' => $request->tgl_masak,
                'status' => 'menunggu',
                'created_at' => now(),
            ]);

            // Insert detail permintaan
            foreach ($request->bahan_id as $index => $bahanId) {
                DB::table('permintaan_detail')->insert([
                    'permintaan_id' => $permintaanId,
                    'bahan_id' => $bahanId,
                    'jumlah_diminta' => $request->jumlah_diminta[$index],
                ]);
            }

            DB::commit();

            return redirect()->route('dapur.permintaan.index')
                           ->with('success', 'Permintaan bahan baku berhasil diajukan!');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error store permintaan: ' . $e->getMessage());
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Gagal mengajukan permintaan: ' . $e->getMessage());
        }
    }

    public function showPermintaan($id)
    {
        $userId = Auth::id();
        
        // Ambil data permintaan milik user yang login
        $permintaan = DB::selectOne("
            SELECT * FROM permintaan 
            WHERE id = ? AND pemohon_id = ?
        ", [$id, $userId]);

        if (!$permintaan) {
            abort(404, 'Permintaan tidak ditemukan atau bukan milik Anda');
        }

        // Ambil detail bahan baku untuk permintaan ini
        $details = DB::select("
            SELECT pd.*, bb.nama as bahan_nama, bb.satuan, bb.jumlah as stok_tersedia
            FROM permintaan_detail pd
            JOIN bahan_baku bb ON pd.bahan_id = bb.id
            WHERE pd.permintaan_id = ?
            ORDER BY bb.nama ASC
        ", [$id]);

        return view('dapur.permintaan.show', compact('permintaan', 'details'));
    }

    public function editPermintaan($id)
    {
        $userId = Auth::id();
        
        // Ambil data permintaan milik user yang login
        $permintaan = DB::selectOne("
            SELECT * FROM permintaan 
            WHERE id = ? AND pemohon_id = ?
        ", [$id, $userId]);

        if (!$permintaan) {
            abort(404, 'Permintaan tidak ditemukan atau bukan milik Anda');
        }

        // Cek apakah masih bisa diedit (status menunggu)
        if ($permintaan->status != 'menunggu') {
            return redirect()->route('dapur.permintaan.show', $id)
                           ->with('error', 'Permintaan yang sudah diproses tidak dapat diedit');
        }

        // Cast ke object Carbon untuk tanggal
        $permintaan = (object) $permintaan;
        $permintaan->tgl_masak = \Carbon\Carbon::parse($permintaan->tgl_masak);

        // Ambil detail permintaan yang sudah ada
        $details = DB::select("
            SELECT pd.*, bb.nama as bahan_nama 
            FROM permintaan_detail pd
            JOIN bahan_baku bb ON pd.bahan_id = bb.id
            WHERE pd.permintaan_id = ?
        ", [$id]);

        // Ambil semua bahan baku (hilangkan filter status)
        $bahanBaku = DB::select("
            SELECT * FROM bahan_baku 
            ORDER BY nama ASC
        ");
        
        return view('dapur.permintaan.edit', compact('permintaan', 'details', 'bahanBaku'));
    }

    public function updatePermintaan(Request $request, $id)
    {
        $userId = Auth::id();
        
        // Validasi permintaan milik user
        $permintaan = DB::selectOne("
            SELECT * FROM permintaan 
            WHERE id = ? AND pemohon_id = ? AND status = 'menunggu'
        ", [$id, $userId]);

        if (!$permintaan) {
            abort(404, 'Permintaan tidak ditemukan, bukan milik Anda, atau sudah diproses');
        }

        $request->validate([
            'menu_makan' => 'required|string|max:255',
            'jumlah_porsi' => 'required|integer|min:1',
            'tgl_masak' => 'required|date|after_or_equal:today',
            'bahan_id' => 'required|array|min:1',
            'bahan_id.*' => 'required|integer|exists:bahan_baku,id',
            'jumlah_diminta' => 'required|array|min:1',
            'jumlah_diminta.*' => 'required|integer|min:1',
        ], [
            'menu_makan.required' => 'Menu makan wajib diisi',
            'jumlah_porsi.required' => 'Jumlah porsi wajib diisi',
            'jumlah_porsi.min' => 'Jumlah porsi minimal 1',
            'tgl_masak.required' => 'Tanggal masak wajib diisi',
            'tgl_masak.after_or_equal' => 'Tanggal masak tidak boleh di masa lalu',
            'bahan_id.required' => 'Minimal pilih 1 bahan baku',
            'jumlah_diminta.required' => 'Jumlah diminta wajib diisi',
            'jumlah_diminta.*.min' => 'Jumlah diminta minimal 1',
        ]);

        try {
            DB::beginTransaction();
            
            // Update tabel permintaan
            DB::table('permintaan')->where('id', $id)->update([
                'menu_makan' => $request->menu_makan,
                'jumlah_porsi' => $request->jumlah_porsi,
                'tgl_masak' => $request->tgl_masak,
            ]);

            // Hapus detail lama
            DB::table('permintaan_detail')->where('permintaan_id', $id)->delete();

            // Insert detail baru
            foreach ($request->bahan_id as $index => $bahanId) {
                DB::table('permintaan_detail')->insert([
                    'permintaan_id' => $id,
                    'bahan_id' => $bahanId,
                    'jumlah_diminta' => $request->jumlah_diminta[$index],
                ]);
            }

            DB::commit();

            return redirect()->route('dapur.permintaan.show', $id)
                           ->with('success', 'Permintaan berhasil diperbarui!');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Gagal memperbarui permintaan. Silakan coba lagi.');
        }
    }

    public function destroyPermintaan($id)
    {
        $userId = Auth::id();
        
        // Validasi permintaan milik user dan masih bisa dihapus
        $permintaan = DB::selectOne("
            SELECT * FROM permintaan 
            WHERE id = ? AND pemohon_id = ? AND status = 'menunggu'
        ", [$id, $userId]);

        if (!$permintaan) {
            abort(404, 'Permintaan tidak ditemukan, bukan milik Anda, atau sudah diproses');
        }

        try {
            DB::beginTransaction();
            
            // Hapus detail terlebih dahulu
            DB::table('permintaan_detail')->where('permintaan_id', $id)->delete();
            
            // Hapus permintaan
            DB::table('permintaan')->where('id', $id)->delete();

            DB::commit();

            return redirect()->route('dapur.permintaan.index')
                           ->with('success', 'Permintaan berhasil dibatalkan!');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                           ->with('error', 'Gagal membatalkan permintaan. Silakan coba lagi.');
        }
    }
}