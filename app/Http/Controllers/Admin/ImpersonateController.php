<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ImpersonateController extends Controller
{
    /**
     * Stop impersonation: restore original admin session.
     */
    public function stop(Request $request): RedirectResponse
    {
        $adminId = session()->pull('impersonate_admin_id');

        // Log out from guru guard
        Auth::guard('guru')->logout();

        if ($adminId) {
            $admin = Admin::find($adminId);
            if ($admin) {
                // Re-authenticate as the original admin
                Auth::guard('admin')->login($admin);
            }
        }

        return redirect()->route('admin.dashboard')
            ->with('toast', [
                'variant' => 'sukses',
                'message' => 'Berhasil kembali ke akses Admin.'
            ]);
    }
}
