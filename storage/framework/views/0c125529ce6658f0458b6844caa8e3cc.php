<div class="container mx-auto px-4 py-6">
    <!-- Card Informasi dengan Tombol Kembali -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <div class="flex items-start gap-4">
            <div class="shrink-0">
                <div class="w-12 h-12 rounded-lg flex items-center justify-center" style="background-color: #E8F5F1;">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" style="color: #1B5E4F;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
            </div>
            <div class="flex-1 min-w-0">
                <h2 class="text-lg font-semibold mb-1" style="font-family: 'Plus Jakarta Sans', sans-serif; color: #1A2422;">
                    Profil Sekolah
                </h2>
                <p class="text-sm text-gray-600 leading-relaxed">
                    Kelola informasi identitas, alamat, kontak, dan data legal sekolah. Data ini digunakan untuk keperluan administrasi dan pencetakan dokumen resmi.
                </p>
            </div>
            <div class="shrink-0">
                <a href="<?php echo e(route('admin.dashboard')); ?>" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Kembali
                </a>
            </div>
        </div>
    </div>

    <!-- Form -->
    <form wire:submit.prevent="save" class="bg-white rounded-lg shadow-md p-6">

        
        <div class="mb-8">
            <h2 class="text-lg font-semibold text-tinta mb-4 border-b pb-2">Identitas Sekolah</h2>
            <div class="space-y-3">

                
                <div class="grid grid-cols-12 gap-3 items-start">
                    <label class="col-span-3 text-sm font-medium text-gray-700 text-right pt-2">Nama Sekolah <span class="text-delima">*</span></label>
                    <span class="col-span-1 text-sm font-medium text-gray-700 text-center pt-2">:</span>
                    <div class="col-span-8">
                        <input
                            type="text"
                            wire:model.live="nama_sekolah"
                            class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-zamrud transition-colors <?php $__errorArgs = ['nama_sekolah'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-400 bg-red-50 <?php else: ?> border-gray-300 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                            placeholder="Contoh: SMP Islam Terpadu Al-Hijrah"
                        >
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['nama_sekolah'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <span class="text-red-500 text-xs mt-1 block"><?php echo e($message); ?></span>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>

                
                <div class="grid grid-cols-12 gap-3 items-start">
                    <label class="col-span-3 text-sm font-medium text-gray-700 text-right pt-2">NPSN <span class="text-delima">*</span></label>
                    <span class="col-span-1 text-sm font-medium text-gray-700 text-center pt-2">:</span>
                    <div class="col-span-8">
                        <input
                            type="text"
                            wire:model.live="npsn"
                            maxlength="8"
                            inputmode="numeric"
                            x-on:input="$el.value = $el.value.replace(/[^0-9]/g, '').slice(0, 8)"
                            class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-zamrud transition-colors <?php $__errorArgs = ['npsn'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-400 bg-red-50 <?php else: ?> border-gray-300 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                            placeholder="12345678"
                        >
                        <div class="flex items-center justify-between mt-1">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['npsn'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <span class="text-red-500 text-xs"><?php echo e($message); ?></span>
                            <?php else: ?>
                                <span></span>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <span class="text-xs text-gray-400"><?php echo e(strlen($npsn ?? '')); ?>/8</span>
                        </div>
                    </div>
                </div>

                
                <div class="grid grid-cols-12 gap-3 items-start">
                    <label class="col-span-3 text-sm font-medium text-gray-700 text-right pt-2">Status Sekolah <span class="text-delima">*</span></label>
                    <span class="col-span-1 text-sm font-medium text-gray-700 text-center pt-2">:</span>
                    <div class="col-span-8">
                        <select
                            wire:model.live="status_sekolah"
                            class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-zamrud transition-colors <?php $__errorArgs = ['status_sekolah'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-400 bg-red-50 <?php else: ?> border-gray-300 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                        >
                            <option value="">Pilih Status</option>
                            <option value="Negeri">Negeri</option>
                            <option value="Swasta">Swasta</option>
                        </select>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['status_sekolah'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <span class="text-red-500 text-xs mt-1 block"><?php echo e($message); ?></span>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>

                
                <div class="grid grid-cols-12 gap-3 items-start">
                    <label class="col-span-3 text-sm font-medium text-gray-700 text-right pt-2">Akreditasi <span class="text-delima">*</span></label>
                    <span class="col-span-1 text-sm font-medium text-gray-700 text-center pt-2">:</span>
                    <div class="col-span-8">
                        <select
                            wire:model.live="akreditasi"
                            class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-zamrud transition-colors <?php $__errorArgs = ['akreditasi'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-400 bg-red-50 <?php else: ?> border-gray-300 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                        >
                            <option value="">Pilih Akreditasi</option>
                            <option value="A">A</option>
                            <option value="B">B</option>
                            <option value="C">C</option>
                            <option value="Belum Terakreditasi">Belum Terakreditasi</option>
                        </select>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['akreditasi'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <span class="text-red-500 text-xs mt-1 block"><?php echo e($message); ?></span>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>

            </div>
        </div>

        
        <div class="mb-8">
            <h2 class="text-lg font-semibold text-tinta mb-4 border-b pb-2">Alamat</h2>
            <div class="space-y-3">

                
                <div class="grid grid-cols-12 gap-3 items-start">
                    <label class="col-span-3 text-sm font-medium text-gray-700 text-right pt-2">Jalan <span class="text-delima">*</span></label>
                    <span class="col-span-1 text-sm font-medium text-gray-700 text-center pt-2">:</span>
                    <div class="col-span-8">
                        <input
                            type="text"
                            wire:model.live="alamat_jalan"
                            class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-zamrud transition-colors <?php $__errorArgs = ['alamat_jalan'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-400 bg-red-50 <?php else: ?> border-gray-300 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                            placeholder="Jl. Raya Pendidikan No. 123"
                        >
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['alamat_jalan'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <span class="text-red-500 text-xs mt-1 block"><?php echo e($message); ?></span>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>

                
                <div class="grid grid-cols-12 gap-3 items-start">
                    <label class="col-span-3 text-sm font-medium text-gray-700 text-right pt-2">Dusun</label>
                    <span class="col-span-1 text-sm font-medium text-gray-700 text-center pt-2">:</span>
                    <div class="col-span-8">
                        <input
                            type="text"
                            wire:model.live="alamat_dusun"
                            class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-zamrud transition-colors"
                        >
                    </div>
                </div>

                
                <div class="grid grid-cols-12 gap-3 items-start">
                    <label class="col-span-3 text-sm font-medium text-gray-700 text-right pt-2">RT / RW</label>
                    <span class="col-span-1 text-sm font-medium text-gray-700 text-center pt-2">:</span>
                    <div class="col-span-8 grid grid-cols-2 gap-2">
                        <div>
                            <input
                                type="text"
                                wire:model.live="alamat_rt"
                                maxlength="3"
                                inputmode="numeric"
                                x-on:input="$el.value = $el.value.replace(/[^0-9]/g, '').slice(0, 3)"
                                class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-zamrud transition-colors <?php $__errorArgs = ['alamat_rt'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-400 bg-red-50 <?php else: ?> border-gray-300 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                placeholder="001"
                            >
                            <div class="flex items-center justify-between mt-1">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['alamat_rt'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <span class="text-red-500 text-xs"><?php echo e($message); ?></span>
                                <?php else: ?>
                                    <span></span>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <span class="text-xs text-gray-400"><?php echo e(strlen($alamat_rt ?? '')); ?>/3</span>
                            </div>
                        </div>
                        <div>
                            <input
                                type="text"
                                wire:model.live="alamat_rw"
                                maxlength="3"
                                inputmode="numeric"
                                x-on:input="$el.value = $el.value.replace(/[^0-9]/g, '').slice(0, 3)"
                                class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-zamrud transition-colors <?php $__errorArgs = ['alamat_rw'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-400 bg-red-50 <?php else: ?> border-gray-300 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                placeholder="002"
                            >
                            <div class="flex items-center justify-between mt-1">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['alamat_rw'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <span class="text-red-500 text-xs"><?php echo e($message); ?></span>
                                <?php else: ?>
                                    <span></span>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <span class="text-xs text-gray-400"><?php echo e(strlen($alamat_rw ?? '')); ?>/3</span>
                            </div>
                        </div>
                    </div>
                </div>

                
                <div class="grid grid-cols-12 gap-3 items-start">
                    <label class="col-span-3 text-sm font-medium text-gray-700 text-right pt-2">Desa/Kelurahan <span class="text-delima">*</span></label>
                    <span class="col-span-1 text-sm font-medium text-gray-700 text-center pt-2">:</span>
                    <div class="col-span-8">
                        <input
                            type="text"
                            wire:model.live="alamat_desa_kelurahan"
                            class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-zamrud transition-colors <?php $__errorArgs = ['alamat_desa_kelurahan'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-400 bg-red-50 <?php else: ?> border-gray-300 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                        >
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['alamat_desa_kelurahan'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <span class="text-red-500 text-xs mt-1 block"><?php echo e($message); ?></span>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>

                
                <div class="grid grid-cols-12 gap-3 items-start">
                    <label class="col-span-3 text-sm font-medium text-gray-700 text-right pt-2">Kecamatan <span class="text-delima">*</span></label>
                    <span class="col-span-1 text-sm font-medium text-gray-700 text-center pt-2">:</span>
                    <div class="col-span-8">
                        <input
                            type="text"
                            wire:model.live="alamat_kecamatan"
                            class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-zamrud transition-colors <?php $__errorArgs = ['alamat_kecamatan'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-400 bg-red-50 <?php else: ?> border-gray-300 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                        >
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['alamat_kecamatan'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <span class="text-red-500 text-xs mt-1 block"><?php echo e($message); ?></span>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>

                
                <div class="grid grid-cols-12 gap-3 items-start">
                    <label class="col-span-3 text-sm font-medium text-gray-700 text-right pt-2">Kabupaten/Kota <span class="text-delima">*</span></label>
                    <span class="col-span-1 text-sm font-medium text-gray-700 text-center pt-2">:</span>
                    <div class="col-span-8">
                        <input
                            type="text"
                            wire:model.live="alamat_kabupaten_kota"
                            class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-zamrud transition-colors <?php $__errorArgs = ['alamat_kabupaten_kota'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-400 bg-red-50 <?php else: ?> border-gray-300 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                        >
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['alamat_kabupaten_kota'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <span class="text-red-500 text-xs mt-1 block"><?php echo e($message); ?></span>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>

                
                <div class="grid grid-cols-12 gap-3 items-start">
                    <label class="col-span-3 text-sm font-medium text-gray-700 text-right pt-2">Provinsi <span class="text-delima">*</span></label>
                    <span class="col-span-1 text-sm font-medium text-gray-700 text-center pt-2">:</span>
                    <div class="col-span-8">
                        <input
                            type="text"
                            wire:model.live="alamat_provinsi"
                            class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-zamrud transition-colors <?php $__errorArgs = ['alamat_provinsi'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-400 bg-red-50 <?php else: ?> border-gray-300 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                        >
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['alamat_provinsi'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <span class="text-red-500 text-xs mt-1 block"><?php echo e($message); ?></span>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>

                
                <div class="grid grid-cols-12 gap-3 items-start">
                    <label class="col-span-3 text-sm font-medium text-gray-700 text-right pt-2">Kode Pos</label>
                    <span class="col-span-1 text-sm font-medium text-gray-700 text-center pt-2">:</span>
                    <div class="col-span-8">
                        <input
                            type="text"
                            wire:model.live="alamat_kode_pos"
                            maxlength="5"
                            inputmode="numeric"
                            x-on:input="$el.value = $el.value.replace(/[^0-9]/g, '').slice(0, 5)"
                            class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-zamrud transition-colors <?php $__errorArgs = ['alamat_kode_pos'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-400 bg-red-50 <?php else: ?> border-gray-300 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                            placeholder="12345"
                        >
                        <div class="flex items-center justify-between mt-1">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['alamat_kode_pos'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <span class="text-red-500 text-xs"><?php echo e($message); ?></span>
                            <?php else: ?>
                                <span></span>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <span class="text-xs text-gray-400"><?php echo e(strlen($alamat_kode_pos ?? '')); ?>/5</span>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        
        <div class="mb-8">
            <h2 class="text-lg font-semibold text-tinta mb-4 border-b pb-2">Kontak</h2>
            <div class="space-y-3">

                
                <div class="grid grid-cols-12 gap-3 items-start">
                    <label class="col-span-3 text-sm font-medium text-gray-700 text-right pt-2">No Telepon <span class="text-delima">*</span></label>
                    <span class="col-span-1 text-sm font-medium text-gray-700 text-center pt-2">:</span>
                    <div class="col-span-8">
                        <input
                            type="text"
                            wire:model.live="no_telepon"
                            maxlength="13"
                            inputmode="numeric"
                            x-on:input="$el.value = $el.value.replace(/[^0-9]/g, '').slice(0, 13)"
                            class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-zamrud transition-colors <?php $__errorArgs = ['no_telepon'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-400 bg-red-50 <?php else: ?> border-gray-300 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                            placeholder="Contoh: 08123456789"
                        >
                        <div class="flex items-center justify-between mt-1">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['no_telepon'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <span class="text-red-500 text-xs"><?php echo e($message); ?></span>
                            <?php else: ?>
                                <span></span>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <span class="text-xs text-gray-400"><?php echo e(strlen($no_telepon ?? '')); ?>/13</span>
                        </div>
                    </div>
                </div>

                
                <div class="grid grid-cols-12 gap-3 items-start">
                    <label class="col-span-3 text-sm font-medium text-gray-700 text-right pt-2">Email</label>
                    <span class="col-span-1 text-sm font-medium text-gray-700 text-center pt-2">:</span>
                    <div class="col-span-8">
                        <input
                            type="email"
                            wire:model.live="email"
                            class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-zamrud transition-colors <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-400 bg-red-50 <?php else: ?> border-gray-300 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                            placeholder="info@sekolah.sch.id"
                        >
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <span class="text-red-500 text-xs mt-1 block"><?php echo e($message); ?></span>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>

                
                <div class="grid grid-cols-12 gap-3 items-start">
                    <label class="col-span-3 text-sm font-medium text-gray-700 text-right pt-2">Website</label>
                    <span class="col-span-1 text-sm font-medium text-gray-700 text-center pt-2">:</span>
                    <div class="col-span-8">
                        <input
                            type="url"
                            wire:model.live="website"
                            class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-zamrud transition-colors <?php $__errorArgs = ['website'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-400 bg-red-50 <?php else: ?> border-gray-300 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                            placeholder="https://www.sekolah.sch.id"
                        >
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['website'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <span class="text-red-500 text-xs mt-1 block"><?php echo e($message); ?></span>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>

            </div>
        </div>

        
        <div class="flex items-center justify-between gap-3">
            <a href="<?php echo e(route('admin.dashboard')); ?>" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali
            </a>
            <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['variant' => 'primary','type' => 'submit']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'primary','type' => 'submit']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                <span wire:loading.remove wire:target="save"><?php echo e($isExisting ? 'Perbarui' : 'Simpan'); ?> Profil Sekolah</span>
                <span wire:loading wire:target="save">Menyimpan...</span>
             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $attributes = $__attributesOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__attributesOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $component = $__componentOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__componentOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
        </div>

    </form>
</div>
<?php /**PATH C:\laragon\www\Sistem-Informasi-Tahfidz-SMP\resources\views/livewire/admin/master-data/sekolah/profil-sekolah.blade.php ENDPATH**/ ?>