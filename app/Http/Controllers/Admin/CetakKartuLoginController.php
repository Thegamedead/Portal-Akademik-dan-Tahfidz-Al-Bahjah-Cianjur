<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\Siswa;
use App\Models\Sekolah;
use Illuminate\Http\Request;

class CetakKartuLoginController extends Controller
{
    public function cetak(Request $request)
    {
        $tipe = $request->query('tipe');
        $mode = $request->query('mode');
        $ids = array_filter(explode(',', $request->query('ids', '')));

        if (!in_array($tipe, ['guru', 'siswa'])) {
            return redirect()->back()->with('error', 'Data tidak valid.');
        }

        $query = $tipe === 'guru' ? Guru::query() : Siswa::with(['kelas', 'kelompok']);

        // Selalu hanya cetak yang memiliki password generate
        $query->whereNotNull('password_text');

        if ($mode !== 'all') {
            if (empty($ids)) {
                return redirect()->back()->with('error', 'Tidak ada data dipilih.');
            }
            $query->whereIn('id', $ids);
        }

        $akuns = $query->get();

        if ($akuns->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada akun dengan password sistem yang bisa dicetak.');
        }

        // Ambil data sekolah singleton
        $sekolah = Sekolah::getInstance();

        return view('admin.cetak.kartu-login', compact('akuns', 'tipe', 'sekolah'));
    }
}
