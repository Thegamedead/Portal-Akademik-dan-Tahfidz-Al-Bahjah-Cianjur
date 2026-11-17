<div class="fixed inset-0 flex items-center justify-center z-50 pointer-events-none" style="pointer-events: none;">
    <div class="bg-white rounded-lg w-full max-w-md p-6" style="pointer-events: auto;">
        <h3 class="text-lg font-semibold mb-4 text-delima">Konfirmasi Hapus</h3>

        <p class="mb-4">{{ $deleteKelasInfo }}</p>
        <p class="text-sm text-gray-600 mb-4">Apakah Anda yakin ingin menghapus kelas ini?</p>

        <div class="flex justify-end gap-3 mt-6">
            <button wire:click="$set('showDeleteConfirm', false)" class="inline-flex items-center justify-center px-5 py-2.5 text-sm font-medium rounded-lg text-white transition-colors focus:outline-none" style="background-color: #9B3A3A; border: 1.5px solid #9B3A3A;">
                Batal
            </button>
            <button wire:click="deleteKelas" class="inline-flex items-center justify-center px-5 py-2.5 text-sm font-medium rounded-lg text-white transition-colors focus:outline-none" style="background-color: #1B5E4F; border: 1.5px solid #1B5E4F;">
                Hapus
            </button>
        </div>
    </div>
</div>
