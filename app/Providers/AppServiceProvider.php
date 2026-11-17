<?php

namespace App\Providers;

use App\Models\Setoran;
use App\Models\Kelompok;
use App\Observers\SetoranObserver;
use App\Observers\KelompokObserver;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Bind UsulanKenaikanTingkatService interface to implementation
        $this->app->bind(
            \App\Services\Contracts\UsulanKenaikanTingkatServiceInterface::class,
            \App\Services\UsulanKenaikanTingkatService::class
        );

        // Bind NotifikasiService interface to implementation
        $this->app->bind(
            \App\Services\Contracts\NotifikasiServiceInterface::class,
            \App\Services\NotifikasiService::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // ── Set Locale for Carbon ──
        Carbon::setLocale('id');

        // ── Global date display macro: $date->tgl() → "25/08/2026" ──
        Carbon::macro('tgl', function () {
            /** @var Carbon $this */
            return $this->translatedFormat('d F Y');
        });

        // ── Global datetime macro: $date->tglJam() → "25/08/2026 14:30" ──
        Carbon::macro('tglJam', function () {
            /** @var Carbon $this */
            return $this->translatedFormat('d F Y H:i');
        });

        // ── Blade directive: @tgl($date) → "25/08/2026" (handles null) ──
        \Illuminate\Support\Facades\Blade::directive('tgl', function ($expression) {
            return "<?php echo ($expression) ? \Carbon\Carbon::parse($expression)->translatedFormat('d F Y') : '—'; ?>";
        });

        // ── Blade directive: @tglJam($date) → "25/08/2026 14:30" ──
        \Illuminate\Support\Facades\Blade::directive('tglJam', function ($expression) {
            return "<?php echo ($expression) ? \Carbon\Carbon::parse($expression)->translatedFormat('d F Y H:i') : '—'; ?>";
        });

        // ── Database Query Logging (Phase 7) ──
        if (config('app.env') === 'local') {
            DB::listen(function ($query) {
                // Log jika query memakan waktu lebih dari 100ms
                if ($query->time > 100) {
                    \Illuminate\Support\Facades\Log::warning('Slow query: ' . $query->sql, [
                        'bindings' => $query->bindings,
                        'time'     => $query->time . 'ms',
                    ]);
                }
            });
        }

        // ── Livewire Macros ──
        \Livewire\Component::macro('toast', function ($message, $type = 'info', $title = null) {
            $this->dispatch('toast', [
                'message' => $message,
                'type' => $type,
                'title' => $title
            ]);
        });

        \Livewire\Component::macro('dialog', function ($action, $payload = null, $title = 'Konfirmasi', $message = 'Apakah Anda yakin?', $type = 'confirm', $confirmText = 'Konfirmasi', $confirmColor = 'blue') {
            $this->dispatch('dialog-open', [
                'type' => $type,
                'title' => $title,
                'message' => $message,
                'payload' => [
                    'action' => $action,
                    'data' => $payload
                ],
                'confirmText' => $confirmText,
                'cancelText' => 'Batal',
                'confirmColor' => $confirmColor
            ]);
        });

        \Livewire\Component::macro('confirmDelete', function ($action, $payload = null, $itemName = 'item ini') {
            $this->dialog(
                $action,
                $payload,
                'Hapus Data',
                "Apakah Anda yakin ingin menghapus {$itemName}? Tindakan ini tidak dapat dibatalkan.",
                'delete',
                'Ya, Hapus',
                'red'
            );
        });

        \Livewire\Component::macro('confirmLogout', function ($action = 'logout') {
            $this->dialog(
                $action,
                null,
                'Konfirmasi Logout',
                'Apakah Anda yakin ingin keluar dari sesi ini?',
                'logout',
                'Ya, Keluar',
                'gray'
            );
        });

        // ── Register Model Observers ──
        Setoran::observe(SetoranObserver::class);
        Kelompok::observe(KelompokObserver::class);

        // ── Audit Log: Login/Logout Events ──
        Event::listen(Login::class, function (Login $event) {
            $guardName = $event->guard;
            $userId = $event->user->getAuthIdentifier();

            // Update semua sesi aktif user ini dengan guard name
            DB::table('sessions')
                ->where('user_id', $userId)
                ->whereNull('guard')
                ->update(['guard' => $guardName]);

            // Record audit log
            try {
                activity('auth')
                    ->causedBy($event->user instanceof \Illuminate\Database\Eloquent\Model ? $event->user : null)
                    ->withProperties([
                        'guard'      => $guardName,
                        'ip_address' => request()->ip(),
                        'user_agent' => request()->userAgent(),
                    ])
                    ->log('Login: ' . ($event->user->name ?? $event->user->getAuthIdentifier()));
            } catch (\Throwable $e) {
                // Jangan sampai audit log gagal memblokir login
            }
        });

        Event::listen(Logout::class, function (Logout $event) {
            if (!$event->user) return;
            try {
                activity('auth')
                    ->causedBy($event->user instanceof \Illuminate\Database\Eloquent\Model ? $event->user : null)
                    ->withProperties(['guard' => $event->guard])
                    ->log('Logout: ' . ($event->user->name ?? $event->user->getAuthIdentifier()));
            } catch (\Throwable $e) {
                // Jangan sampai audit log gagal memblokir logout
            }
        });

        // ── Gate Definitions: Role-based Access Control ──

        /**
         * Gate: view-all-kelompok
         *
         * - Admin: dapat melihat semua kelompok (always true)
         * - Guru: hanya dapat melihat kelompok binaan (check kelompoksBimbingan)
         * - Siswa: hanya dapat melihat kelompok sendiri (check kelompok_id)
         */
        Gate::define('view-all-kelompok', function ($user) {
            // Jika user adalah Admin, selalu izinkan
            if (auth()->guard('admin')->check()) {
                return true;
            }

            // Jika user adalah Guru, hanya izinkan melihat kelompok binaan
            if (auth()->guard('guru')->check()) {
                // Guru tidak bisa "view all", hanya view kelompok binaan
                return false;
            }

            // Jika user adalah Siswa, hanya izinkan melihat kelompok sendiri
            if (auth()->guard('siswa')->check()) {
                return false;
            }

            return false;
        });

        /**
         * Gate: edit-absensi-setoran
         *
         * - Admin: dapat edit kapan saja (always true)
         * - Guru: dapat edit dalam jendela edit (sampai tengah malam H+0)
         * - Siswa: tidak dapat edit (never)
         */
        Gate::define('edit-absensi-setoran', function ($user, $tanggal = null) {
            // Jika user adalah Admin, selalu izinkan
            if (auth()->guard('admin')->check()) {
                return true;
            }

            // Jika user adalah Guru, cek jendela edit
            if (auth()->guard('guru')->check()) {
                // Jika tidak ada tanggal yang diberikan, anggap hari ini
                $tanggalAbsensi = $tanggal ? Carbon::parse($tanggal) : now();

                // Jendela edit: sampai tengah malam H+0
                // Guru dapat edit jika tanggal absensi adalah hari ini DAN waktu sekarang <= 23:59:59
                $isToday = $tanggalAbsensi->isToday();
                $isBeforeMidnight = now()->format('H:i:s') <= '23:59:59';

                return $isToday && $isBeforeMidnight;
            }

            // Jika user adalah Siswa, tidak izinkan edit
            if (auth()->guard('siswa')->check()) {
                return false;
            }

            return false;
        });
    }
}
