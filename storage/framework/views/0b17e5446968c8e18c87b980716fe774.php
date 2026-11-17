

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showModalKonfirmasiKeluar && $siswaRegistrasiKeluar): ?>
    <div
        class="fixed inset-0 z-50 flex items-center justify-center p-4 pointer-events-none"
        role="dialog"
        aria-modal="true"
        aria-labelledby="modal-konfirmasi-keluar-title"
    >
        
        <div
            class="absolute inset-0 bg-transparent"
            wire:click="tutupModalKonfirmasiKeluar"
            aria-hidden="true"
        ></div>

        
        <div
            class="relative w-full max-w-md rounded-2xl bg-white shadow-xl"
            style="font-family:'Inter',sans-serif;"
        >
            
            <div class="flex items-center gap-3 px-6 pt-6 pb-4 rounded-t-2xl" style="background-color:#FFFBEB;border-bottom:1px solid #FCD34D;">
                <div class="flex-shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" style="color:#C9A227;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <h3 id="modal-konfirmasi-keluar-title" class="text-base font-semibold"
                    style="font-family:'Plus Jakarta Sans',sans-serif;color:white;">
                    Konfirmasi Aksi Kritikal
                </h3>
            </div>

            
            <div class="px-6 py-4">
                
                <p class="text-sm mb-4" style="color:#1A2422;">
                    Registrasi keluar adalah <strong>aksi kritikal</strong> yang akan menonaktifkan akun siswa
                    dan mencabut semua sesi login aktif.
                </p>

                
                <div class="mb-4 p-3 rounded-lg text-sm space-y-1.5" style="background-color:#F4F5F4;">
                    <div style="color:#1A2422;">
                        <span class="font-medium">Siswa:</span>
                        <span><?php echo e($siswaRegistrasiKeluar->nama); ?></span>
                    </div>
                    <div style="color:#1A2422;">
                        <span class="font-medium">NIS:</span>
                        <span><?php echo e($siswaRegistrasiKeluar->nis); ?></span>
                    </div>
                    <div style="color:#1A2422;">
                        <span class="font-medium">Jenis Keluar:</span>
                        <span>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = \App\Enums\JenisKeluar::options(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($value === $jenisKeluar): ?>
                                    <?php echo e($label); ?>

                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </span>
                    </div>
                    <div style="color:#1A2422;">
                        <span class="font-medium">Tanggal Keluar:</span>
                        <span><?php echo e(\Carbon\Carbon::parse($tanggalKeluar)->tgl()); ?></span>
                    </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($jenisKeluar === 'Pindah Sekolah' && $tujuanSekolah): ?>
                        <div style="color:#1A2422;">
                            <span class="font-medium">Tujuan Sekolah:</span>
                            <span><?php echo e($tujuanSekolah); ?></span>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                
                <p class="text-sm font-medium mb-2" style="color:#1A2422;">
                    Konsekuensi:
                </p>
                <ul class="text-sm space-y-1 list-disc list-inside" style="color:#6B7280;">
                    <li>
                        Status Akademik berubah menjadi
                        <strong style="color:#1A2422;">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($jenisKeluar === 'Lulus SMP'): ?>
                                Lulus SMP
                            <?php elseif($jenisKeluar === 'Pindah Sekolah'): ?>
                                Pindah Sekolah
                            <?php else: ?>
                                Keluar
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </strong>
                    </li>
                    <li>Akun siswa akan <strong style="color:#1A2422;">dinonaktifkan</strong> (tidak bisa login)</li>
                    <li>Semua sesi login aktif akan <strong style="color:#1A2422;">dicabut otomatis</strong></li>
                    <li>Data historis siswa (absensi, setoran, progres hafalan) tetap <strong style="color:#1A2422;">tersimpan</strong></li>
                </ul>

                
                <div class="mt-4 p-3 rounded-lg" style="background-color:#FEF3C7;border-left:3px solid #C9A227;">
                    <p class="text-xs font-medium" style="color:#78350F;">
                        ⚠️ Aksi ini tidak dapat dibatalkan setelah diproses.
                    </p>
                </div>
            </div>

            
            <div class="flex gap-3 px-6 py-4" style="border-top:1px solid #E5E7EB;">
                <button
                    type="button"
                    wire:click="tutupModalKonfirmasiKeluar"
                    class="btn-modal-cancel flex-1"
                >
                    Batal
                </button>
                <button
                    type="button"
                    wire:click="prosesRegistrasiKeluar"
                    class="btn-modal-confirm flex-1"
                >
                    Ya, Proses Registrasi Keluar
                </button>
            </div>
        </div>
    </div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php /**PATH C:\laragon\www\Sistem-Informasi-Tahfidz-SMP\resources\views/livewire/admin/master-data/siswa/partials/modal-konfirmasi-keluar.blade.php ENDPATH**/ ?>