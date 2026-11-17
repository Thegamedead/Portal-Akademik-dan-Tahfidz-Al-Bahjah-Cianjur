<div
    x-data="{
        isOpen: false,
        type: 'confirm',
        title: '',
        message: '',
        payload: null,
        confirmText: 'Konfirmasi',
        cancelText: 'Batal',
        confirmColor: 'blue', // 'red', 'green', 'blue', 'gray'
        noBackdrop: true,
        
        open(data) {
            this.type = data.type || 'confirm';
            this.title = data.title || '';
            this.message = data.message || '';
            this.payload = data.payload || null;
            this.confirmText = data.confirmText || 'Konfirmasi';
            this.cancelText = data.cancelText || 'Batal';
            this.confirmColor = data.confirmColor || 'blue';
            this.noBackdrop = data.noBackdrop || false;
            this.isOpen = true;
        },
        close() {
            this.isOpen = false;
        },
        confirm() {
            if (this.payload && this.payload.action) {
                Livewire.dispatch(this.payload.action, { payload: this.payload.data || null });
            }
            this.close();
        }
    }"
    @dialog-open.window="open(Array.isArray($event.detail) ? $event.detail[0] : $event.detail)"
    @dialog-close.window="close()"
    x-show="isOpen"
    class="relative z-[9999]"
    aria-labelledby="dialog-title"
    role="dialog"
    aria-modal="true"
    style="display: none; z-index: 99999 !important;"
>
    <!-- Backdrop -->
    <div
        x-show="isOpen && !noBackdrop"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 pointer-events-none"
        @click="close()"
    ></div>

    <div class="fixed inset-0 z-[9999] w-screen overflow-y-auto pointer-events-none" :class="noBackdrop ? 'pointer-events-none' : ''" style="z-index: 99999 !important;">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <!-- Modal Panel -->
            <div
                x-show="isOpen"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                @keydown.escape.window="close()"
                class="relative transform overflow-hidden rounded-lg bg-white px-4 pb-4 pt-5 text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:p-6 pointer-events-auto"
                :class="noBackdrop ? 'border border-gray-200' : ''"
            >
                <div class="sm:flex sm:items-start">
                    <!-- Icon based on type/color -->
                    <div
                        class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full sm:mx-0 sm:h-10 sm:w-10"
                        :class="{
                            'bg-red-100': confirmColor === 'red',
                            'bg-green-100': confirmColor === 'green',
                            'bg-blue-100': confirmColor === 'blue',
                            'bg-gray-100': confirmColor === 'gray'
                        }"
                    >
                        <!-- Warning/Red Icon -->
                        <svg x-show="confirmColor === 'red'" class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <!-- Success/Green Icon -->
                        <svg x-show="confirmColor === 'green'" class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                        </svg>
                        <!-- Info/Blue Icon -->
                        <svg x-show="confirmColor === 'blue'" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                        </svg>
                        <!-- General/Gray Icon -->
                        <svg x-show="confirmColor === 'gray'" class="h-6 w-6 text-gray-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.625 9.75a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375m-13.5 3.01c0 1.6 1.123 2.994 2.707 3.227 1.087.16 2.185.283 3.293.369V21l4.184-4.183a1.14 1.14 0 01.778-.332 48.294 48.294 0 005.83-.498c1.585-.233 2.708-1.626 2.708-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z" />
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left w-full">
                        <h3 class="text-base font-semibold leading-6 text-gray-900" id="dialog-title" x-text="title"></h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500" x-html="message"></p>
                        </div>
                        
                        <!-- Extra content based on type (e.g. input-reason) -->
                        <template x-if="type === 'input-reason'">
                            <div class="mt-4">
                                <label class="block text-sm font-medium text-gray-700">Alasan / Keterangan</label>
                                <textarea x-model="payload.reason" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#1B5E4F] focus:ring focus:ring-[#1B5E4F] focus:ring-opacity-50" rows="3" placeholder="Masukkan alasan Anda..."></textarea>
                            </div>
                        </template>
                    </div>
                </div>
                <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                    <button
                        type="button"
                        class="inline-flex w-full justify-center rounded-md px-3 py-2 text-sm font-semibold text-white shadow-sm sm:ml-3 sm:w-auto transition-colors"
                        :class="{
                            'bg-red-600 hover:bg-red-500': confirmColor === 'red',
                            'bg-[#1B5E4F] hover:bg-[#134237]': confirmColor === 'green',
                            'bg-blue-600 hover:bg-blue-500': confirmColor === 'blue',
                            'bg-gray-600 hover:bg-gray-500': confirmColor === 'gray'
                        }"
                        @click="confirm()"
                        x-text="confirmText"
                    ></button>
                    <button
                        type="button"
                        class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto"
                        @click="close()"
                        x-text="cancelText"
                    ></button>
                </div>
            </div>
        </div>
    </div>
</div>
<?php /**PATH C:\laragon\www\Sistem-Informasi-Tahfidz-SMP\resources\views\components\dialog-container.blade.php ENDPATH**/ ?>