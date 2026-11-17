<div>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isOpen): ?>
        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isFlexibleDate): ?>
            <style>
                /* Style for filled dates (green dot) */
                <?php $__currentLoopData = $filledDates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $date): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    input[type="date"][value="<?php echo e($date); ?>"]::-webkit-calendar-picker-indicator {
                        filter: opacity(0.8);
                    }
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                /* Note: Native date pickers don't support custom styling per date easily.
                   We'll use a visual indicator below the input instead */
            </style>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <div class="fixed inset-0 z-40 flex items-center justify-center p-4 sm:p-6 pointer-events-none"
             x-data="{ show: false }"
             x-init="setTimeout(() => show = true, 50)"
             x-show="show"
             x-transition:enter="transform transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transform transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95">

            <div class="w-full max-w-4xl max-h-full pointer-events-auto bg-gray-50 flex flex-col rounded-2xl border border-gray-200 relative shadow-2xl overflow-hidden">

                
                <div class="px-6 py-4 bg-white border-b border-gray-200 flex items-center justify-between shrink-0">
                    <div>
                        <h2 class="text-xl font-bold text-[#1A2422] font-jakarta">Absensi dan Setoran Hafalan</h2>
                        <div class="flex items-center gap-3 mt-1">
                            <span class="text-sm font-semibold text-gray-700"><?php echo e($kelompok->nama ?? ''); ?></span>
                            <span class="text-gray-300">•</span>
                            <span class="flex items-center gap-1.5 text-sm font-medium text-gray-500">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                <?php echo e($kelompok->guruPembimbing->nama ?? 'Belum ditentukan'); ?>

                            </span>
                        </div>
                    </div>
                    <button type="button" wire:click="closeModal" class="bg-white rounded-md text-gray-400 hover:text-gray-500 border border-gray-200 p-1.5 shadow-sm transition-colors hover:bg-gray-50">
                        <span class="sr-only">Tutup</span>
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                
                <div class="flex-1 overflow-y-auto p-6 space-y-6">

                    
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                        <div class="flex items-stretch divide-x divide-gray-100">
                            
                            <div class="px-6 py-4">
                                <p class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-2">Tanggal</p>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isFlexibleDate): ?>
                                    
                                    <div class="relative">
                                        <div class="flex items-center gap-2 bg-white border border-gray-200 rounded-lg px-3 py-2 shadow-sm cursor-pointer hover:border-gray-300 transition-colors">
                                            <svg class="h-4 w-4 text-zamrud flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                            <span class="text-sm font-semibold text-[#1A2422] flex-1">
                                                <?php echo e(\Carbon\Carbon::parse($tanggal)->locale('id')->translatedFormat('d F Y')); ?>

                                            </span>
                                            <svg class="h-3.5 w-3.5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                            </svg>
                                        </div>
                                        <input type="date"
                                               wire:model.live="tanggal"
                                               onclick="this.showPicker()"
                                               class="absolute inset-0 opacity-0 cursor-pointer z-10 w-full h-full">

                                        
                                        <div class="mt-2">
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(in_array($tanggal, $liburDates)): ?>
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[9px] font-bold bg-purple-100 text-purple-700 border border-purple-200">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-purple-500"></span>
                                                    Tanggal Libur
                                                </span>
                                            <?php elseif(in_array($tanggal, $filledDates)): ?>
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[9px] font-bold bg-green-100 text-green-700 border border-green-200">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                                    Sudah Terisi
                                                </span>
                                            <?php else: ?>
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[9px] font-bold bg-gray-100 text-gray-600 border border-gray-200">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>
                                                    Belum Terisi
                                                </span>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    
                                    <p class="text-sm font-semibold text-[#1A2422] pt-1">
                                        <?php echo e(\Carbon\Carbon::parse($tanggal)->locale('id')->translatedFormat('d F Y')); ?>

                                    </p>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>

                            
                            <div class="px-6 py-4">
                                <p class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-1.5">Hari</p>
                                <p class="text-sm font-semibold text-[#1A2422] pt-1">
                                    <?php echo e(\Carbon\Carbon::parse($tanggal)->locale('id')->translatedFormat('l')); ?>

                                </p>
                            </div>

                            
                            <div class="px-6 py-4">
                                <p class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-2">Waktu</p>
                                <div class="flex items-center gap-2">
                                    <input type="time"
                                           wire:model="waktuMulai"
                                           step="60"
                                           pattern="[0-9]{2}:[0-9]{2}"
                                           class="text-sm font-semibold text-[#1A2422] border border-gray-200 rounded-lg px-2 py-1.5 focus:outline-none focus:ring-2 focus:ring-[#1B5E4F]/30 focus:border-[#1B5E4F] bg-white">
                                    <span class="text-gray-400 text-sm">—</span>
                                    <input type="time"
                                           wire:model="waktuSelesai"
                                           step="60"
                                           pattern="[0-9]{2}:[0-9]{2}"
                                           class="text-sm font-semibold text-[#1A2422] border border-gray-200 rounded-lg px-2 py-1.5 focus:outline-none focus:ring-2 focus:ring-[#1B5E4F]/30 focus:border-[#1B5E4F] bg-white">
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(is_null($targetSiswaId)): ?>
                        
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 flex items-center justify-between"
                             style="<?php echo e($isLiburPilihan ? 'border-color:#7c3aed;background:#faf5ff;' : ''); ?>">
                            <div>
                                <h3 class="text-sm font-bold" style="<?php echo e($isLiburPilihan ? 'color:#6d28d9;' : 'color:#111827;'); ?>">
                                    <?php echo e($isLiburPilihan ? '🗓️ Hari Libur Aktif' : 'Tandai sebagai Hari Libur'); ?>

                                </h3>
                                <p class="text-xs mt-0.5" style="color:#6b7280;">
                                    Jika diaktifkan, sesi ini akan ditandai libur untuk semua siswa dan tidak ada setoran.
                                </p>
                            </div>
                            
                            <button
                                type="button"
                                wire:click="toggleLibur"
                                <?php echo e($isMinggu ? 'disabled' : ''); ?>

                                title="<?php echo e($isMinggu ? 'Hari Minggu otomatis libur' : ($isLiburPilihan ? 'Klik untuk nonaktifkan libur' : 'Klik untuk tandai libur')); ?>"
                                style="
                                    position: relative;
                                    display: inline-flex;
                                    align-items: center;
                                    width: 52px;
                                    height: 28px;
                                    border-radius: 9999px;
                                    border: none;
                                    cursor: <?php echo e($isMinggu ? 'not-allowed' : 'pointer'); ?>;
                                    transition: background-color 0.2s ease;
                                    background-color: <?php echo e($isLiburPilihan ? '#7c3aed' : '#d1d5db'); ?>;
                                    opacity: <?php echo e($isMinggu ? '0.5' : '1'); ?>;
                                    flex-shrink: 0;
                                "
                            >
                                <span style="
                                    position: absolute;
                                    top: 3px;
                                    left: <?php echo e($isLiburPilihan ? '27px' : '3px'); ?>;
                                    width: 22px;
                                    height: 22px;
                                    border-radius: 9999px;
                                    background: white;
                                    box-shadow: 0 1px 3px rgba(0,0,0,0.2);
                                    transition: left 0.2s ease;
                                "></span>
                            </button>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    
                    
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session()->has('success')): ?>
                        <div class="bg-zamrud/10 border border-zamrud rounded-lg p-4">
                            <p class="text-sm text-zamrud"><?php echo e(session('success')); ?></p>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isMinggu): ?>
                        <div class="flex flex-col items-center justify-center gap-3 bg-red-50 border border-red-200 rounded-xl p-8 text-center">
                            <div class="w-14 h-14 rounded-full bg-red-100 flex items-center justify-center">
                                <svg class="w-7 h-7 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </div>
                            <div>
                                <p class="text-base font-bold text-red-800">Hari Minggu</p>
                                <p class="text-sm text-red-600 mt-1">Hari Minggu adalah hari libur. Tidak ada kegiatan tahfidz.</p>
                            </div>
                            <p class="text-xs text-red-500">Pilih tanggal lain untuk mengisi absensi.</p>
                        </div>
                    <?php elseif($isLiburPilihan): ?>
                        <div class="flex flex-col items-center justify-center gap-3 bg-purple-50 border border-purple-200 rounded-xl p-8 text-center">
                            <div class="w-14 h-14 rounded-full bg-purple-100 flex items-center justify-center">
                                <svg class="w-7 h-7 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>
                            <div>
                                <p class="text-base font-bold text-purple-800">Hari Libur</p>
                                <p class="text-sm text-purple-600 mt-1">Tanggal ini ditandai sebagai hari libur. Semua siswa tercatat <strong>Libur</strong> dan tidak ada setoran hafalan.</p>
                            </div>
                            <p class="text-xs text-purple-500">Matikan toggle di atas untuk mengisi absensi normal.</p>
                        </div>
                    <?php else: ?>

                    
                    <div class="bg-white rounded-lg shadow overflow-hidden">
                        
                        <div class="px-5 py-3 bg-gray-50 border-b border-gray-200 flex items-center justify-between gap-3">
                            <p class="text-sm text-gray-500">
                                Total: <strong class="text-gray-800"><?php echo e($this->activeSiswas->count()); ?> siswa</strong>
                            </p>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$isLiburPilihan && !$isMinggu && is_null($targetSiswaId)): ?>
                                <button wire:click="hadirSemua"
                                        type="button"
                                        class="inline-flex items-center gap-2 px-4 py-2 bg-zamrud hover:bg-zamrud/90 text-white text-sm font-medium rounded-lg transition-colors shadow-sm active:scale-95">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Hadir Semua
                                </button>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-[#1B5E4F] text-white">
                                    <tr>
                                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider w-10">No</th>
                                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Siswa</th>
                                        <th scope="col" class="px-4 py-3 text-center text-xs font-medium uppercase tracking-wider">Status Kehadiran</th>
                                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Hasil Setoran</th>
                                        <th scope="col" class="px-4 py-3 text-center text-xs font-medium uppercase tracking-wider">Aksi Setoran</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $this->activeSiswas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $siswa): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                        <?php
                                            $statusSiswa = $absensis[$siswa->id] ?? '';
                                            $sudahHadir  = $statusSiswa === 'Hadir';
                                            $targetSelesai = $siswaCompletedStatus[$siswa->id] ?? false;
                                        ?>
                                        <tr class="hover:bg-gray-50 transition-colors <?php echo e($targetSelesai ? 'bg-green-50/30' : ''); ?>">
                                            <td class="px-4 py-3 text-sm text-gray-500"><?php echo e($index + 1); ?></td>
                                            <td class="px-4 py-3 text-sm font-medium text-gray-900">
                                                <div class="flex items-center gap-2">
                                                    <div>
                                                        <?php echo e($siswa->nama); ?><br>
                                                        <span class="text-xs text-gray-500 font-normal font-mono"><?php echo e($siswa->nis); ?></span>
                                                    </div>
                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($targetSelesai): ?>
                                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 text-[10px] font-bold text-green-700 bg-green-100 border border-green-300 rounded-full whitespace-nowrap" title="Siswa ini sudah menyelesaikan seluruh target hafalan kelompok. Tidak dapat input absensi/setoran lagi. Proses kelulusan di menu Progres Siswa.">
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                            </svg>
                                                            Tuntas
                                                        </span>
                                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                </div>
                                            </td>

                                            
                                            <td class="px-4 py-3 text-center">
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($targetSelesai): ?>
                                                    <div class="text-center" title="Siswa sudah menyelesaikan seluruh target hafalan kelompok. Input absensi/setoran dinonaktifkan. Untuk proses kelulusan, buka menu Progres Siswa.">
                                                        <span class="inline-block px-3 py-1.5 text-xs font-semibold text-green-700 bg-green-100 border border-green-300 rounded-md cursor-help">
                                                            Target Selesai ✓
                                                        </span>
                                                        <p class="text-[10px] text-gray-500 mt-1">Proses kelulusan di menu Progres Siswa</p>
                                                    </div>
                                                <?php else: ?>
                                                    <select wire:model.live="absensis.<?php echo e($siswa->id); ?>"
                                                            class="block w-full min-w-[110px] pl-3 pr-8 py-1.5 text-sm border-gray-300 focus:outline-none focus:ring-zamrud focus:border-zamrud rounded-md shadow-sm
                                                                   <?php echo e(match($statusSiswa) {
                                                                        'Hadir' => 'bg-green-50 text-green-800 border-green-300',
                                                                        'Izin'  => 'bg-blue-50 text-blue-800 border-blue-300',
                                                                        'Sakit' => 'bg-yellow-50 text-yellow-800 border-yellow-300',
                                                                        'Alfa'  => 'bg-red-50 text-red-800 border-red-300',
                                                                        'Libur' => 'bg-purple-50 text-purple-800 border-purple-300',
                                                                        default => 'bg-gray-50 text-gray-500 border-gray-300',
                                                                   }); ?>"
                                                            <?php echo e($isLiburPilihan || $isMinggu ? 'disabled' : ''); ?>>
                                                        <option value="">-- Pilih --</option>
                                                        <option value="Hadir">Hadir</option>
                                                        <option value="Izin">Izin</option>
                                                        <option value="Sakit">Sakit</option>
                                                        <option value="Alfa">Alfa</option>
                                                        <option value="Libur">Libur</option>
                                                    </select>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </td>

                                            
                                            <td class="px-4 py-3 text-sm">
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($targetSelesai): ?>
                                                    <div class="text-center" title="Seluruh target hafalan untuk kelompok ini sudah diselesaikan oleh siswa. Tidak ada setoran baru yang dapat diinput.">
                                                        <span class="text-xs text-green-600 font-semibold cursor-help">✓ Semua target selesai</span>
                                                    </div>
                                                <?php elseif(isset($setoranSiswa[$siswa->id]) && count($setoranSiswa[$siswa->id]) > 0): ?>
                                                    <div class="space-y-1">
                                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $setoranSiswa[$siswa->id]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $setoran): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                                            <div class="text-[11px] text-gray-700 font-medium">
                                                                <?php echo e($setoran['surah_awal'] ?? $setoran['surah_id']); ?>: <?php echo e($setoran['ayat_awal'] ?? $setoran['ayat_mulai']); ?>-<?php echo e($setoran['ayat_akhir'] ?? $setoran['ayat_selesai']); ?>

                                                            </div>
                                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                                    </div>
                                                <?php else: ?>
                                                    <span class="text-[11px] text-gray-400 italic">Kosong</span>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </td>

                                            
                                            <td class="px-4 py-3 text-center">
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($targetSelesai): ?>
                                                    <span class="text-xs text-gray-400 italic cursor-help" title="Input setoran tidak tersedia karena siswa sudah menyelesaikan seluruh target hafalan kelompok">-</span>
                                                <?php elseif($sudahHadir): ?>
                                                    <button wire:click="$dispatch('openSetoranModal', { siswaId: <?php echo e($siswa->id); ?>, kelompokId: <?php echo e($this->kelompokId); ?>, date: '<?php echo e($tanggal); ?>' })"
                                                       class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg text-white bg-[#C9A227] hover:bg-[#b8911f] active:scale-95 transition-all shadow-sm cursor-pointer whitespace-nowrap">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                                        </svg>
                                                        Input Setoran
                                                    </button>
                                                <?php elseif(!$sudahHadir && $statusSiswa !== ''): ?>
                                                    <span class="text-xs text-gray-400 italic">Tidak hadir</span>
                                                <?php else: ?>
                                                    <span class="inline-flex items-center px-3 py-1.5 text-xs rounded-lg bg-gray-100 text-gray-400 cursor-not-allowed whitespace-nowrap"
                                                          title="Pilih status Hadir terlebih dahulu">
                                                        Input Setoran
                                                    </span>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?> 

                </div>

                
                <div class="px-6 py-4 bg-white border-t border-gray-200 flex flex-col sm:flex-row items-center justify-between gap-4 shrink-0">
                    <p class="text-xs text-gray-500">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isMinggu): ?>
                            <span class="text-red-600 font-medium">🚫 Hari Minggu — tidak ada kegiatan.</span>
                        <?php elseif($isLiburPilihan && $absensiSudahDisimpan): ?>
                            <span class="text-purple-700 font-medium">🗓️ Hari libur telah tersimpan. Matikan toggle untuk mengisi absensi normal.</span>
                        <?php elseif($absensiSudahDisimpan && $justSaved): ?>
                            <span class="text-green-700 font-medium">✔ Absensi tersimpan. Klik tombol kuning untuk input setoran siswa yang hadir.</span>
                        <?php else: ?>
                            <span class="text-amber-600 font-medium">⚡ Pilih status siswa untuk langsung aktifkan setoran.</span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </p>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$isLiburPilihan && !$isMinggu): ?>
                        <button wire:click="save"
                                type="button"
                                wire:loading.attr="disabled"
                                class="px-6 py-2 bg-zamrud hover:bg-zamrud/90 text-white rounded-lg transition-colors duration-200 font-semibold shadow-sm w-full sm:w-auto disabled:opacity-70 active:scale-95">
                            <span wire:loading.remove wire:target="save">Simpan</span>
                            <span wire:loading wire:target="save">Menyimpan...</span>
                        </button>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

            </div>
        </div>

    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('admin.setoran.modal-setoran', []);

$__key = null;

$__key ??= \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::generateKey('lw-2592771649-0', $__key);

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
<?php /**PATH C:\laragon\www\Sistem-Informasi-Tahfidz-SMP\resources\views/livewire/admin/absensi/input-absensi.blade.php ENDPATH**/ ?>