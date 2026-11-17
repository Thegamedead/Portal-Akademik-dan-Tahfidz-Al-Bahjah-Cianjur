<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo e($title ?? 'Dashboard'); ?> — <?php echo e(config('app.name', 'Sistem Tahfidz')); ?></title>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    <?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::styles(); ?>

    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://npmcdn.com/flatpickr/dist/l10n/id.js"></script>
    <style>
        /* Fokus input flatpickr */
        input.flatpickr-input:focus,
        .flatpickr-input.form-control:focus,
        input[type="text"].flatpickr-input:focus {
            border-color: #1B5E4F !important;
            box-shadow: 0 0 0 2px rgba(27, 94, 79, 0.2) !important;
        }
        /* Flatpickr Calendar z-index tinggi agar tidak terpotong modal */
        .flatpickr-calendar {
            z-index: 99999 !important;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15) !important;
        }
        /* Pastikan flatpickr calendar tidak terpotong */
        .flatpickr-calendar.open {
            display: inline-block !important;
        }

        /* Standardisasi input[type="month"] agar konsisten di semua browser */
        input[type="month"] {
            font-family: 'Inter', sans-serif;
            font-size: 0.875rem;
            font-weight: 600;
            color: #1A2422;
            border: 1px solid #E5E7EB;
            border-radius: 0.5rem;
            padding: 0.375rem 0.75rem;
            background-color: #FFFFFF;
            height: 36px;
            outline: none;
            cursor: pointer;
        }
        input[type="month"]:focus {
            border-color: #1B5E4F;
            box-shadow: 0 0 0 3px rgba(27,94,79,0.15);
        }
        input[type="month"]::-webkit-calendar-picker-indicator {
            cursor: pointer;
            opacity: 0.7;
            filter: invert(25%) sepia(60%) saturate(500%) hue-rotate(120deg);
        }

        /* Standardisasi Warna Tombol Modal Global */
        .btn-modal-cancel {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            padding: 0.625rem 1.25rem !important; /* py-2.5 px-5 */
            font-size: 0.875rem !important; /* text-sm */
            font-weight: 500 !important; /* font-medium */
            border-radius: 0.5rem !important; /* rounded-lg */
            color: #ffffff !important;
            background-color: #9B3A3A !important;
            border: 1.5px solid #9B3A3A !important;
            transition: all 0.2s ease !important;
            cursor: pointer !important;
        }
        .btn-modal-cancel:hover {
            background-color: #7A2D2D !important;
            border-color: #7A2D2D !important;
        }

        .btn-modal-confirm {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            padding: 0.625rem 1.25rem !important;
            font-size: 0.875rem !important;
            font-weight: 500 !important;
            border-radius: 0.5rem !important;
            color: #ffffff !important;
            background-color: #1B5E4F !important;
            border: 1.5px solid #1B5E4F !important;
            transition: all 0.2s ease !important;
            cursor: pointer !important;
        }
        .btn-modal-confirm:hover {
            background-color: #164A3C !important;
            border-color: #164A3C !important;
        }
    </style>
    <script>
        function initFlatpickr() {
            document.querySelectorAll('input[type="date"]').forEach(function(el) {
                if (el.classList.contains('flatpickr-input') && el.type === 'hidden') return;

                // Skip if element has data-no-flatpickr or class no-flatpickr
                if (el.hasAttribute('data-no-flatpickr') || el.classList.contains('no-flatpickr')) {
                    return;
                }

                var existingValue = el.value || '';
                if (el._flatpickr) {
                    if (existingValue && el._flatpickr.selectedDates.length === 0) {
                        el._flatpickr.setDate(existingValue, false);
                    }
                    return;
                }

                // Check if element has data-max-date attribute for custom maxDate
                var maxDateValue = el.getAttribute('data-max-date') || null;

                flatpickr(el, {
                    dateFormat: 'Y-m-d',
                    altInput: true,
                    altFormat: 'd F Y',
                    allowInput: false,
                    locale: 'id',
                    defaultDate: existingValue || null,
                    maxDate: maxDateValue,
                    disableMobile: false,
                    // ── Aktifkan dropdown bulan & tahun ──
                    monthSelectorType: 'dropdown',
                    // ── Tombol shortcut Hari Ini ──
                    plugins: [
                        new ShortcutButtonsPlugin({
                            button: [
                                { label: 'Hari Ini' }
                            ],
                            onClick: function(index, fp) {
                                fp.setDate(new Date(), true);
                                fp.close();
                            }
                        })
                    ],
                    onChange: function(selectedDates, dateStr, instance) {
                        instance.element.dispatchEvent(new Event('input', { bubbles: true }));
                        instance.element.dispatchEvent(new Event('change', { bubbles: true }));
                    },
                    onReady: function(selectedDates, dateStr, fp) {
                        var clearBtn = document.createElement('button');
                        clearBtn.type = 'button';
                        clearBtn.textContent = '✕ Hapus';
                        clearBtn.style.cssText = 'display:block;width:100%;padding:6px 10px;margin-top:4px;font-size:11px;color:#9B3A3A;background:#FFF0F0;border:none;border-top:1px solid #FFE4E4;cursor:pointer;text-align:center;font-family:Inter,sans-serif;border-bottom-left-radius:8px;border-bottom-right-radius:8px;';
                        clearBtn.addEventListener('click', function() {
                            fp.clear();
                            fp.close();
                            fp.element.dispatchEvent(new Event('input', { bubbles: true }));
                            fp.element.dispatchEvent(new Event('change', { bubbles: true }));
                        });
                        fp.calendarContainer.appendChild(clearBtn);
                    }
                });
            });
        }

        // Load ShortcutButtonsPlugin
        (function() {
            if (typeof ShortcutButtonsPlugin === 'undefined') {
                var link = document.createElement('link');
                link.rel = 'stylesheet';
                link.href = 'https://cdn.jsdelivr.net/npm/shortcut-buttons-flatpickr@0.4.0/dist/themes/light.min.css';
                document.head.appendChild(link);
                var script = document.createElement('script');
                script.src = 'https://cdn.jsdelivr.net/npm/shortcut-buttons-flatpickr@0.4.0/dist/shortcut-buttons-flatpickr.min.js';
                script.onload = function() { initFlatpickr(); };
                document.head.appendChild(script);
            }
        })();

        document.addEventListener('DOMContentLoaded', initFlatpickr);
        document.addEventListener('livewire:navigated', initFlatpickr);
        document.addEventListener('livewire:init', () => {
            Livewire.hook('commit', ({ succeed }) => {
                succeed(() => {
                    setTimeout(initFlatpickr, 50);
                });
            });
        });
    </script>
    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('sekolah-updated', () => {
                window.location.reload();
            });
            Livewire.on('tahun-ajaran-changed', () => {
                window.location.reload();
            });
        });
    </script>
</head>
<body
    class="h-full antialiased"
    style="background-color: #F4F5F4; color: #1A2422; font-family: 'Inter', sans-serif;"
    x-data="{
        sidebarOpen: false,
        sidebarCollapsed: false,
        guruNama: '<?php echo e(addslashes(auth('guru')->user()?->nama ?? 'Guru')); ?>',
        guruEmail: '<?php echo e(addslashes(auth('guru')->user()?->email ?? '')); ?>',
        guruNiy: '<?php echo e(addslashes(auth('guru')->user()?->niy ?? '')); ?>',
        guruInisial: '<?php echo e(addslashes(strtoupper(substr(auth('guru')->user()?->nama ?? 'G', 0, 1)))); ?>',
        get isDesktop() { return window.innerWidth >= 1024; },
        init() {
            if (this.isDesktop) {
                this.sidebarOpen = true;
                const saved = localStorage.getItem('guruSidebarCollapsed');
                this.sidebarCollapsed = saved === 'true';
            }
            window.addEventListener('resize', () => {
                if (window.innerWidth >= 1024) {
                    this.sidebarOpen = true;
                } else {
                    this.sidebarOpen = false;
                    this.sidebarCollapsed = false;
                }
            });
        },
        toggleCollapse() {
            if (window.innerWidth >= 1024) {
                this.sidebarCollapsed = !this.sidebarCollapsed;
                localStorage.setItem('guruSidebarCollapsed', this.sidebarCollapsed);
            } else {
                this.sidebarOpen = !this.sidebarOpen;
            }
        }
    }"
    @profile-updated.window="guruNama = $event.detail.nama; guruEmail = $event.detail.email; guruNiy = $event.detail.niy; guruInisial = $event.detail.inisial;"
>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session()->has('impersonate_admin_id')): ?>
    <div class="fixed top-0 left-0 right-0 z-[200] flex items-center justify-between px-4 py-2 text-white text-sm font-semibold" style="background-color: #9B3A3A;">
        <div class="flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
            </svg>
            <span>Mode Tukar Akses: Anda sedang login sebagai <strong><?php echo e(auth('guru')->user()?->nama ?? 'Guru'); ?></strong></span>
        </div>
        <a href="<?php echo e(route('admin.impersonate.stop')); ?>" class="flex items-center gap-1.5 rounded-md px-3 py-1 text-xs font-bold transition-colors" style="background-color: rgba(255,255,255,0.2); hover:background-color: rgba(255,255,255,0.3);">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M11 17l-5-5m0 0l5-5m-5 5h12" />
            </svg>
            Kembali ke Admin
        </a>
    </div>
    <div style="height: 40px;"></div> 
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <div
        x-show="sidebarOpen && !isDesktop"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click="sidebarOpen = false"
        class="fixed inset-0 z-30 lg:hidden pointer-events-none"
        style="display: none;"
        aria-hidden="true"
    ></div>

    
    <aside
        :class="{
            '-translate-x-full': !sidebarOpen,
            'translate-x-0': sidebarOpen,
            'w-64': !sidebarCollapsed,
            'w-16': sidebarCollapsed
        }"
        class="fixed inset-y-0 left-0 z-40 flex flex-col transition-all duration-300 ease-in-out bg-white shadow-lg lg:translate-x-0"
        style="border-right: 1px solid #E5E7EB;"
        aria-label="Navigasi guru"
    >
        
        <?php
            $sekolah = \App\Models\Sekolah::getInstance();
        ?>
        <div
            class="flex h-20 items-center shrink-0 overflow-hidden transition-all duration-300"
            :class="{ 'gap-3 px-4': !sidebarCollapsed, 'justify-center': sidebarCollapsed }"
            style="border-bottom: 1px solid #E5E7EB;"
        >
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($sekolah && $sekolah->logo): ?>
                <div class="shrink-0 rounded-full bg-white border-2 border-gray-200 shadow-md flex items-center justify-center overflow-hidden transition-all duration-300"
                     :class="{ 'h-14 w-14': !sidebarCollapsed, 'h-8 w-8 border': sidebarCollapsed }">
                    <img src="<?php echo e(Storage::url($sekolah->logo)); ?>" alt="Logo" class="object-contain transition-all duration-300"
                         :class="{ 'h-12 w-12 rounded-full': !sidebarCollapsed, 'h-6 w-6': sidebarCollapsed }">
                </div>
            <?php else: ?>
                <div
                    class="flex h-14 w-14 shrink-0 items-center justify-center rounded-full font-bold text-white text-xl shadow-md border-2 border-gray-200"
                    style="background-color: #1B5E4F; font-family: 'Plus Jakarta Sans', sans-serif;"
                >ST</div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <div x-show="!sidebarCollapsed"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-100"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="flex flex-col overflow-hidden truncate">
                <span class="font-semibold text-sm truncate" style="color: #1A2422; font-family: 'Plus Jakarta Sans', sans-serif;" x-text="guruNama">
                    <?php echo e(auth('guru')->user()?->nama ?? 'Guru'); ?>

                </span>
                <span class="text-xs truncate" style="color: #6B7280; font-family: 'Inter', sans-serif;" x-text="guruNiy">
                    <?php echo e(auth('guru')->user()?->niy ?? ''); ?>

                </span>
                <span class="text-xs font-medium mt-0.5 truncate uppercase tracking-wide text-zamrud">
                    Guru
                </span>
            </div>
        </div>


        
        <nav class="flex-1 py-4 px-2 space-y-1" :class="sidebarCollapsed ? '' : 'overflow-y-auto overflow-x-hidden'">

            
            <div x-data="{ tooltip: false }" class="relative">
                <a
                    href="<?php echo e(route('guru.dashboard')); ?>"
                    @mouseenter="if (sidebarCollapsed) tooltip = true"
                    @mouseleave="tooltip = false"
                    class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-colors group"
                    style="<?php echo e(request()->routeIs('guru.dashboard') ? 'background-color: #E8F5F1; color: #1B5E4F;' : 'color: #374151;'); ?>"
                    onmouseover="if (!this.style.backgroundColor.includes('E8F5F1')) { this.style.backgroundColor='#F4F5F4'; }"
                    onmouseout="if (!this.style.backgroundColor.includes('E8F5F1')) { this.style.backgroundColor='transparent'; }"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <span x-show="!sidebarCollapsed" class="truncate">Dashboard</span>
                </a>
                <div
                    x-show="sidebarCollapsed && tooltip"
                    x-transition:enter="transition ease-out duration-150"
                    x-transition:enter-start="opacity-0 -translate-x-1"
                    x-transition:enter-end="opacity-100 translate-x-0"
                    class="absolute left-full top-1/2 z-50 ml-2 -translate-y-1/2 rounded-lg px-3 py-1.5 text-xs text-white shadow-lg pointer-events-none whitespace-nowrap"
                    style="background-color: #1A2422; display: none;"
                >Dashboard</div>
            </div>

            
            <div x-data="{ tooltip: false }" class="relative mt-1">
                <a
                    href="<?php echo e(route('guru.referensi-juz')); ?>"
                    @mouseenter="if (sidebarCollapsed) tooltip = true"
                    @mouseleave="tooltip = false"
                    class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-colors group"
                    style="<?php echo e(request()->routeIs('guru.referensi-juz') ? 'background-color: #E8F5F1; color: #1B5E4F;' : 'color: #374151;'); ?>"
                    onmouseover="if (!this.style.backgroundColor.includes('E8F5F1')) { this.style.backgroundColor='#F4F5F4'; }"
                    onmouseout="if (!this.style.backgroundColor.includes('E8F5F1')) { this.style.backgroundColor='transparent'; }"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                    <span x-show="!sidebarCollapsed" class="truncate">Referensi Juz</span>
                </a>
                <div x-show="sidebarCollapsed && tooltip" class="absolute left-full top-1/2 z-50 ml-2 -translate-y-1/2 rounded-lg px-3 py-1.5 text-xs text-white shadow-lg pointer-events-none whitespace-nowrap" style="background-color: #1A2422; display: none;">Referensi Juz</div>
            </div>

            
            <?php
                $isPengujiTes = \App\Services\FeatureToggleService::isEnabled('tes_penempatan') && (
                    \App\Models\TesPenempatan::where('guru_penguji_id', auth('guru')->id())->exists() ||
                    (auth('guru')->user()?->tugasTambahans()->where('jenis_tugas', 'like', '%Penguji%')->exists() ?? false)
                );
            ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isPengujiTes): ?>
            <div x-data="{ tooltip: false }" class="relative mt-1">
                <a
                    href="<?php echo e(route('guru.tes-penempatan.index')); ?>"
                    @mouseenter="if (sidebarCollapsed) tooltip = true"
                    @mouseleave="tooltip = false"
                    class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-colors group"
                    style="<?php echo e(request()->routeIs('guru.tes-penempatan*') ? 'background-color: #E8F5F1; color: #1B5E4F;' : 'color: #374151;'); ?>"
                    onmouseover="if (!this.style.backgroundColor.includes('E8F5F1')) { this.style.backgroundColor='#F4F5F4'; }"
                    onmouseout="if (!this.style.backgroundColor.includes('E8F5F1')) { this.style.backgroundColor='transparent'; }"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                    </svg>
                    <span x-show="!sidebarCollapsed" class="truncate">Tes Penempatan</span>
                </a>
                <div x-show="sidebarCollapsed && tooltip" class="absolute left-full top-1/2 z-50 ml-2 -translate-y-1/2 rounded-lg px-3 py-1.5 text-xs text-white shadow-lg pointer-events-none whitespace-nowrap" style="background-color: #1A2422; display: none;">Tes Penempatan</div>
            </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            
            <div x-data="{ tooltip: false }" class="relative mt-1">
                <a
                    href="<?php echo e(route('guru.kelompok-tahfidz.index')); ?>"
                    @mouseenter="if (sidebarCollapsed) tooltip = true"
                    @mouseleave="tooltip = false"
                    class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-colors group"
                    style="<?php echo e(request()->routeIs('guru.kelompok-tahfidz*') && !request()->routeIs('guru.riwayat-lulusan*') ? 'background-color: #E8F5F1; color: #1B5E4F;' : 'color: #374151;'); ?>"
                    onmouseover="if (!this.style.backgroundColor.includes('E8F5F1')) { this.style.backgroundColor='#F4F5F4'; }"
                    onmouseout="if (!this.style.backgroundColor.includes('E8F5F1')) { this.style.backgroundColor='transparent'; }"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <span x-show="!sidebarCollapsed" class="truncate">Kelompok Tahfidz</span>
                </a>
                <div x-show="sidebarCollapsed && tooltip" class="absolute left-full top-1/2 z-50 ml-2 -translate-y-1/2 rounded-lg px-3 py-1.5 text-xs text-white shadow-lg pointer-events-none whitespace-nowrap" style="background-color: #1A2422; display: none;">Kelompok Tahfidz</div>
            </div>

            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth('guru')->user()?->kelompoks()->whereIn('tingkat', [\App\Enums\TingkatKelompok::DASAR->value, \App\Enums\TingkatKelompok::MENENGAH->value])->exists()): ?>
            <div x-data="{ tooltip: false }" class="relative mt-1">
                <a
                    href="<?php echo e(route('guru.usulan-kenaikan.index')); ?>"
                    @mouseenter="if (sidebarCollapsed) tooltip = true"
                    @mouseleave="tooltip = false"
                    class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-colors group"
                    style="<?php echo e(request()->routeIs('guru.usulan-kenaikan*') ? 'background-color: #E8F5F1; color: #1B5E4F;' : 'color: #374151;'); ?>"
                    onmouseover="if (!this.style.backgroundColor.includes('E8F5F1')) { this.style.backgroundColor='#F4F5F4'; }"
                    onmouseout="if (!this.style.backgroundColor.includes('E8F5F1')) { this.style.backgroundColor='transparent'; }"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    </svg>
                    <span x-show="!sidebarCollapsed" class="truncate">Usulan Kenaikan Tingkat</span>
                </a>
                <div x-show="sidebarCollapsed && tooltip" class="absolute left-full top-1/2 z-50 ml-2 -translate-y-1/2 rounded-lg px-3 py-1.5 text-xs text-white shadow-lg pointer-events-none whitespace-nowrap" style="background-color: #1A2422; display: none;">Usulan Kenaikan Tingkat</div>
            </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            
            <div x-data="{ tooltip: false }" class="relative mt-1">
                <a
                    href="<?php echo e(route('guru.riwayat-lulusan.index')); ?>"
                    @mouseenter="if (sidebarCollapsed) tooltip = true"
                    @mouseleave="tooltip = false"
                    class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-colors group"
                    style="<?php echo e(request()->routeIs('guru.riwayat-lulusan*') ? 'background-color: #E8F5F1; color: #1B5E4F;' : 'color: #374151;'); ?>"
                    onmouseover="if (!this.style.backgroundColor.includes('E8F5F1')) { this.style.backgroundColor='#F4F5F4'; }"
                    onmouseout="if (!this.style.backgroundColor.includes('E8F5F1')) { this.style.backgroundColor='transparent'; }"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <span x-show="!sidebarCollapsed" class="truncate">Riwayat Lulusan</span>
                </a>
                <div x-show="sidebarCollapsed && tooltip" class="absolute left-full top-1/2 z-50 ml-2 -translate-y-1/2 rounded-lg px-3 py-1.5 text-xs text-white shadow-lg pointer-events-none whitespace-nowrap" style="background-color: #1A2422; display: none;">Riwayat Lulusan</div>
            </div>

            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth('guru')->user()?->kelompoks()->where('tingkat', \App\Enums\TingkatKelompok::ATAS->value)->exists()): ?>
            <div x-data="{ tooltip: false }" class="relative mt-1">
                <a
                    href="<?php echo e(route('guru.kelulusan-program.index')); ?>"
                    @mouseenter="if (sidebarCollapsed) tooltip = true"
                    @mouseleave="tooltip = false"
                    class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-colors group"
                    style="<?php echo e(request()->routeIs('guru.kelulusan-program*') ? 'background-color: #E8F5F1; color: #1B5E4F;' : 'color: #374151;'); ?>"
                    onmouseover="if (!this.style.backgroundColor.includes('E8F5F1')) { this.style.backgroundColor='#F4F5F4'; }"
                    onmouseout="if (!this.style.backgroundColor.includes('E8F5F1')) { this.style.backgroundColor='transparent'; }"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path d="M12 14l9-5-9-5-9 5 9 5z" />
                        <path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0112 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0112 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222" />
                    </svg>
                    <span x-show="!sidebarCollapsed" class="truncate">Kelulusan Program</span>
                </a>
                <div x-show="sidebarCollapsed && tooltip" class="absolute left-full top-1/2 z-50 ml-2 -translate-y-1/2 rounded-lg px-3 py-1.5 text-xs text-white shadow-lg pointer-events-none whitespace-nowrap" style="background-color: #1A2422; display: none;">Kelulusan Program</div>
            </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        </nav>

        
        <div class="shrink-0 p-3" style="border-top: 1px solid #E5E7EB;">
            <div x-data="{ tooltip: false }" class="relative">
                <a
                    href="<?php echo e(route('guru.profil')); ?>"
                    @mouseenter="if (sidebarCollapsed) tooltip = true"
                    @mouseleave="tooltip = false"
                    class="w-full flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-colors group"
                    style="color: #374151; text-align: left; text-decoration: none;"
                    onmouseover="this.style.backgroundColor='#F4F5F4'; this.style.color='#1B5E4F';"
                    onmouseout="this.style.backgroundColor='transparent'; this.style.color='#374151';"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span x-show="!sidebarCollapsed" class="truncate">Profil Saya</span>
                </a>
                <div x-show="sidebarCollapsed && tooltip" class="absolute left-full top-1/2 z-50 ml-2 -translate-y-1/2 rounded-lg px-3 py-1.5 text-xs text-white shadow-lg pointer-events-none whitespace-nowrap" style="background-color: #1A2422; display: none;">Profil Saya</div>
            </div>
        </div>
    </aside>


    
    <form x-ref="guruLogoutForm" method="POST" action="<?php echo e(route('guru.logout')); ?>" style="display: none;">
        <?php echo csrf_field(); ?>
    </form>

    
    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('logout-confirmed', () => {
                document.querySelector('[x-ref="guruLogoutForm"]').submit();
            });
        });
    </script>

    
    <div
        :class="{
            'lg:pl-64': !sidebarCollapsed,
            'lg:pl-16': sidebarCollapsed
        }"
        class="flex min-h-screen flex-col transition-all duration-300"
    >
        
        <header
            class="sticky top-0 z-20 flex h-20 items-center gap-4 px-4 sm:px-6 bg-white shadow-sm shrink-0"
            style="border-bottom: 1px solid #E5E7EB;"
        >
            
            <button
                type="button"
                @click="toggleCollapse()"
                class="rounded-lg p-2 transition-colors focus:outline-none focus:ring-2"
                style="color: #6B7280;"
                onmouseover="this.style.backgroundColor='#F4F5F4'"
                onmouseout="this.style.backgroundColor='transparent'"
                aria-label="Toggle sidebar"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>

            
            <div class="flex-1 min-w-0 flex items-center">
                
                <div class="flex flex-col justify-center">
                    <h1 class="text-base sm:text-lg font-bold truncate" style="font-family: 'Plus Jakarta Sans', sans-serif; color: #1A2422;">
                        Sistem Informasi Tahfidz
                    </h1>
                    <p class="text-xs sm:text-sm truncate" style="color: #1A2422; font-family: 'Inter', sans-serif;">
                        <?php echo e(\App\Models\Sekolah::first()?->nama_sekolah ?? 'Nama Sekolah Belum Diatur'); ?>

                    </p>
                </div>

                
                <div class="hidden sm:block mx-4 sm:mx-6 shrink-0" style="width: 2px; height: 36px; background-color: #D1D5DB;"></div>

                
                <?php
                    $activeSemester = \App\Models\Semester::with('tahunAjaran')->where('status_aktif', true)->first();
                ?>

                <div class="hidden sm:flex flex-col justify-center" style="font-family: 'Inter', sans-serif;">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($activeSemester): ?>
                        <span class="text-xs sm:text-sm text-gray-800 leading-tight">
                            Tahun Pelajaran <?php echo e($activeSemester->tahunAjaran->label ?? '-'); ?>

                        </span>
                        <span class="text-xs sm:text-sm text-gray-800 leading-tight mt-0.5">
                            Semester <?php echo e(ucfirst($activeSemester->jenis)); ?>

                        </span>
                    <?php else: ?>
                        <span class="text-xs sm:text-sm text-red-600 leading-tight">
                            Belum Ada TA Aktif
                        </span>
                        <span class="text-[10px] sm:text-xs text-red-500 leading-tight mt-0.5">
                            Hubungi Admin
                        </span>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>

            
            <div
                x-data="{
                    hariTanggal: '',
                    jam: '',
                    initClock() {
                        const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                        const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

                        const updateClock = () => {
                            const now = new Date();
                            this.hariTanggal = `${days[now.getDay()]}, ${now.getDate()} ${months[now.getMonth()]} ${now.getFullYear()}`;
                            this.jam = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' }) + ' WIB';
                        };

                        updateClock();
                        setInterval(updateClock, 1000);
                    }
                }"
                x-init="initClock()"
                class="hidden sm:flex flex-col items-end mr-4 pr-5 border-r border-gray-300"
                style="font-family: 'Inter', sans-serif;"
            >
                <span class="text-xs font-bold text-gray-800 pr-3" x-text="hariTanggal">Memuat...</span>
                <span class="text-[11px] font-semibold text-emerald-600 mt-0.5 pr-3" x-text="jam"></span>
            </div>

            
            <div x-data="{ userMenuOpen: false, isHovered: false }" class="relative" @keydown.escape.window="userMenuOpen = false">
                
                <button
                    @click="userMenuOpen = !userMenuOpen"
                    style="cursor: pointer; outline: none; border: none; flex-shrink: 0;"
                    :style="{
                        display: 'flex',
                        alignItems: 'center',
                        justifyContent: 'center',
                        width: '40px',
                        height: '40px',
                        borderRadius: '50%',
                        backgroundColor: '#1B5E4F',
                        color: '#ffffff',
                        fontSize: '16px',
                        fontWeight: '700',
                        fontFamily: '\'Plus Jakarta Sans\', sans-serif',
                        boxShadow: (userMenuOpen || isHovered) ? '0 0 0 2px #fff, 0 0 0 4px #1B5E4F' : 'none',
                        transition: 'box-shadow 0.15s ease'
                    }"
                    @mouseover="isHovered = true"
                    @mouseout="isHovered = false"
                    aria-label="Menu pengguna"
                    :aria-expanded="userMenuOpen.toString()"
                >
                    <span x-text="guruInisial">
                        <?php echo e(strtoupper(substr(auth('guru')->user()?->nama ?? 'G', 0, 1))); ?>

                    </span>
                </button>

                
                <div
                    x-show="userMenuOpen"
                    @click.outside="userMenuOpen = false"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                    x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                    x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
                    class="absolute right-0 top-full mt-2 w-60 rounded-xl bg-white shadow-xl z-50"
                    style="border: 1px solid #E5E7EB; transform-origin: top right; display: none;"
                >
                    
                    <div class="px-4 py-3" style="border-bottom: 1px solid #E5E7EB;">
                        <div class="flex items-center gap-3">
                            <div
                                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full text-white text-base font-bold"
                                style="background-color: #1B5E4F; font-family: 'Plus Jakarta Sans', sans-serif;"
                            >
                                <span x-text="guruInisial">
                                    <?php echo e(strtoupper(substr(auth('guru')->user()?->nama ?? 'G', 0, 1))); ?>

                                </span>
                            </div>
                            <div class="min-w-0">
                                <p class="text-base font-semibold truncate" style="color: #1A2422; font-family: 'Plus Jakarta Sans', sans-serif;" x-text="guruNama">
                                    <?php echo e(auth('guru')->user()?->nama ?? 'Guru'); ?>

                                </p>
                                <p class="text-sm" style="color: #6B7280;" x-text="guruNiy"><?php echo e(auth('guru')->user()?->niy); ?></p>
                            </div>
                        </div>
                    </div>

                    
                    <div class="py-1.5">

                        
                        <button
                            type="button"
                            @click="userMenuOpen = false; $dispatch('open-password-modal')"
                            class="w-full flex items-center gap-3 px-4 py-2.5 text-sm font-medium transition-colors"
                            style="color: #374151; text-align: left; white-space: nowrap; display: flex; align-items: center;"
                            onmouseover="this.style.backgroundColor='#F4F5F4'; this.style.color='#1B5E4F';"
                            onmouseout="this.style.backgroundColor='transparent'; this.style.color='#374151';"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                            Ubah Password
                        </button>

                        
                        <div style="height: 1px; background-color: #E5E7EB; margin: 4px 16px;"></div>

                        
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session()->has('impersonate_admin_id')): ?>
                        <a
                            href="<?php echo e(route('admin.impersonate.stop')); ?>"
                            class="w-full flex items-center gap-3 px-4 py-2.5 text-sm font-medium transition-colors"
                            style="color: #9B3A3A; text-align: left; white-space: nowrap; display: flex; align-items: center;"
                            onmouseover="this.style.backgroundColor='#FDEAEA';"
                            onmouseout="this.style.backgroundColor='transparent';"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 17l-5-5m0 0l5-5m-5 5h12" />
                            </svg>
                            Kembali ke Akses Admin
                        </a>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        
                        <button
                            type="button"
                            @click="userMenuOpen = false; $dispatch('dialog-open', {
                                type: 'confirm',
                                title: 'Konfirmasi Logout',
                                message: 'Apakah Anda yakin ingin keluar dari sistem?',
                                confirmText: 'Ya, Logout',
                                cancelText: 'Batal',
                                confirmColor: 'red',
                                noBackdrop: true,
                                payload: { action: 'logout-confirmed' }
                            })"
                            class="w-full flex items-center gap-3 px-4 py-2.5 text-sm font-medium transition-colors"
                            style="color: #9B3A3A; text-align: left; white-space: nowrap; display: flex; align-items: center;"
                            onmouseover="this.style.backgroundColor='#FDEAEA';"
                            onmouseout="this.style.backgroundColor='transparent';"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                            Logout
                        </button>
                    </div>
                </div>
            </div>
        </header>


        
        <div
            x-data="{ profilModalOpen: false }"
            @open-profil-modal.window="profilModalOpen = true"
            @close-profil-modal.window="profilModalOpen = false"
            @keydown.escape.window="profilModalOpen = false"
        >
            <div
                x-show="profilModalOpen"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 translate-y-2"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 translate-y-2"
                class="fixed top-16 right-4 z-50 w-96 rounded-2xl bg-white shadow-2xl"
                style="border: 1px solid #E5E7EB; display: none;"
                @click.outside="profilModalOpen = false"
            >
                
                <div class="flex items-center justify-between px-5 py-4" style="border-bottom: 1px solid #E5E7EB;">
                    <div class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="#1B5E4F" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                        <h3 class="text-sm font-semibold" style="color: #1A2422; font-family: 'Plus Jakarta Sans', sans-serif;">Profil Saya</h3>
                    </div>
                    <button type="button" @click="profilModalOpen = false" class="text-gray-400 hover:text-gray-600 transition-colors focus:outline-none" aria-label="Tutup">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
                
                <div class="px-5 py-4">
                    <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('guru.profil-form', []);

$__key = null;

$__key ??= \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::generateKey('lw-1862627475-0', $__key);

$__html = app('livewire')->mount($__name, $__params, $__key);

echo $__html;

unset($__html);
unset($__key);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
                </div>
            </div>
        </div>

        
        <div
            x-data="{ passwordModalOpen: false }"
            @open-password-modal.window="passwordModalOpen = true"
            @close-password-modal.window="passwordModalOpen = false"
            @keydown.escape.window="passwordModalOpen = false"
        >
            <div
                x-show="passwordModalOpen"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 translate-y-2"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 translate-y-2"
                class="fixed top-16 right-4 z-50 w-96 rounded-2xl bg-white shadow-2xl"
                style="border: 1px solid #E5E7EB; display: none;"
                @click.outside="passwordModalOpen = false"
            >
                
                <div class="flex items-center justify-between px-5 py-4" style="border-bottom: 1px solid #E5E7EB;">
                    <div class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="#1B5E4F" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                        <h3 class="text-sm font-semibold" style="color: #1A2422; font-family: 'Plus Jakarta Sans', sans-serif;">Ubah Password</h3>
                    </div>
                    <button type="button" @click="passwordModalOpen = false" class="text-gray-400 hover:text-gray-600 transition-colors focus:outline-none" aria-label="Tutup">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
                
                <div class="px-5 py-4">
                    <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('guru.password-form', []);

$__key = null;

$__key ??= \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::generateKey('lw-1862627475-1', $__key);

$__html = app('livewire')->mount($__name, $__params, $__key);

echo $__html;

unset($__html);
unset($__key);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
                </div>
            </div>
        </div>

        
        <main class="flex-1 p-4 sm:p-6">
            <?php echo e($slot); ?>

        </main>
    </div>

    
    <div
        x-data="{
            toasts: [],
            addToast(variant, message) {
                const id = Date.now();
                this.toasts.push({ id, variant, message });
                setTimeout(() => { this.removeToast(id); }, 4400);
            },
            removeToast(id) {
                this.toasts = this.toasts.filter(t => t.id !== id);
            }
        }"
        x-on:toast.window="addToast($event.detail.variant ?? $event.detail[0]?.variant ?? 'sukses', $event.detail.message ?? $event.detail[0]?.message ?? '')"
        class="fixed top-20 right-4 z-60 space-y-2 pointer-events-none"
        aria-live="polite"
    >
        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('toast')): ?>
            <?php if (isset($component)) { $__componentOriginal7cfab914afdd05940201ca0b2cbc009b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7cfab914afdd05940201ca0b2cbc009b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.toast','data' => ['variant' => session('toast')['variant'] ?? 'sukses','message' => session('toast')['message'] ?? '']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('toast'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(session('toast')['variant'] ?? 'sukses'),'message' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(session('toast')['message'] ?? '')]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal7cfab914afdd05940201ca0b2cbc009b)): ?>
<?php $attributes = $__attributesOriginal7cfab914afdd05940201ca0b2cbc009b; ?>
<?php unset($__attributesOriginal7cfab914afdd05940201ca0b2cbc009b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal7cfab914afdd05940201ca0b2cbc009b)): ?>
<?php $component = $__componentOriginal7cfab914afdd05940201ca0b2cbc009b; ?>
<?php unset($__componentOriginal7cfab914afdd05940201ca0b2cbc009b); ?>
<?php endif; ?>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        
        <template x-for="toast in toasts" :key="toast.id">
            <div
                x-data="{ show: true }"
                x-show="show"
                x-init="setTimeout(() => show = false, 4000)"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-x-4"
                x-transition:enter-end="opacity-100 translate-x-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-x-0"
                x-transition:leave-end="opacity-0 translate-x-4"
                class="flex items-start gap-3 rounded-xl px-4 py-3 shadow-lg max-w-sm w-full pointer-events-auto"
                :style="toast.variant === 'error'
                    ? 'background-color:#FDEAEA;border:1px solid #9B3A3A;color:#9B3A3A;'
                    : toast.variant === 'warning'
                        ? 'background-color:#FFF8E1;border:1px solid #C9A227;color:#C9A227;'
                        : 'background-color:#E8F5F1;border:1px solid #1B5E4F;color:#1B5E4F;'"
                role="alert"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mt-0.5 shrink-0" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <template x-if="toast.variant === 'error'">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </template>
                    <template x-if="toast.variant !== 'error'">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </template>
                </svg>
                <p x-text="toast.message" class="text-sm flex-1" style="font-family:'Inter',sans-serif;"></p>
                <button @click="show = false" class="ml-auto shrink-0 -mr-1 -mt-0.5 p-1 rounded hover:bg-black/10 transition-colors" aria-label="Tutup notifikasi">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>
            </div>
        </template>
    </div>

    
    <?php if (isset($component)) { $__componentOriginal2a13903ba2363a39b78d5468899b9a18 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2a13903ba2363a39b78d5468899b9a18 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.dialog-container','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('dialog-container'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal2a13903ba2363a39b78d5468899b9a18)): ?>
<?php $attributes = $__attributesOriginal2a13903ba2363a39b78d5468899b9a18; ?>
<?php unset($__attributesOriginal2a13903ba2363a39b78d5468899b9a18); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal2a13903ba2363a39b78d5468899b9a18)): ?>
<?php $component = $__componentOriginal2a13903ba2363a39b78d5468899b9a18; ?>
<?php unset($__componentOriginal2a13903ba2363a39b78d5468899b9a18); ?>
<?php endif; ?>

    
    <script>
        window.addEventListener('download-file', event => {
            const { content, fileName, mimeType } = event.detail[0] || event.detail;
            const blob = new Blob([content], { type: mimeType || 'text/plain' });
            const url = window.URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url;
            link.download = fileName || 'download.txt';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            window.URL.revokeObjectURL(url);
        });
    </script>

    <?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::scripts(); ?>

</body>
</html>
<?php /**PATH C:\laragon\www\Sistem-Informasi-Tahfidz-SMP\resources\views/guru/layouts/app.blade.php ENDPATH**/ ?>