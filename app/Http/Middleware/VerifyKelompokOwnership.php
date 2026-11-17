<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Kelompok;

/**
 * Middleware untuk verifikasi kepemilikan kelompok oleh Guru.
 *
 * Memastikan bahwa kelompok yang diakses adalah kelompok binaan guru yang sedang login.
 * Jika bukan kelompok binaan, akan return 403 Forbidden.
 */
class VerifyKelompokOwnership
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Ambil guru yang sedang login
        $guru = auth()->guard('guru')->user();

        if (!$guru) {
            abort(403, 'Akses tidak diizinkan.');
        }

        // Ambil kelompok dari route parameter
        // Laravel Route Model Binding bisa mengembalikan model ATAU integer mentah
        $kelompokParam = $request->route('kelompok');

        if (!$kelompokParam) {
            abort(403, 'Kelompok tidak ditemukan.');
        }

        // Handle: sudah jadi Kelompok model (implicit route model binding aktif)
        if ($kelompokParam instanceof Kelompok) {
            $isKelompokBinaan = ((int) $kelompokParam->guru_pembimbing_id === (int) $guru->id);
        } else {
            // Handle: masih berupa integer/string ID
            $isKelompokBinaan = Kelompok::where('id', $kelompokParam)
                ->where('guru_pembimbing_id', $guru->id)
                ->exists();
        }

        if (!$isKelompokBinaan) {
            abort(403, 'Anda tidak memiliki akses ke kelompok ini. Anda hanya dapat mengakses kelompok binaan Anda sendiri.');
        }

        return $next($request);
    }
}
