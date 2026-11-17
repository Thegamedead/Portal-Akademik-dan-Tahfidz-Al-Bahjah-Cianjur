<div class="container mx-auto px-4 py-6">
    <!-- Breadcrumb -->
    <nav class="text-sm mb-4">
        <ol class="flex items-center space-x-2">
            <li><a href="{{ route('admin.dashboard') }}" class="text-gray-600 hover:text-zamrud">Dashboard</a></li>
            <li><span class="text-gray-400">/</span></li>
            <li><a href="#" class="text-gray-600 hover:text-zamrud">Master Data</a></li>
            <li><span class="text-gray-400">/</span></li>
            <li><a href="{{ route('admin.master-data.kelas.index') }}" class="text-gray-600 hover:text-zamrud">Kelas Akademik</a></li>
            <li><span class="text-gray-400">/</span></li>
            <li class="text-zamrud font-medium">Buat Massal</li>
        </ol>
    </nav>

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-tinta">Buat Kelas Massal</h1>
        <p class="text-gray-600 mt-1">Buat beberapa kelas sekaligus untuk satu tingkat</p>
    </div>

    {{-- TODO: Toast notifications handled in Livewire component --}}
    {{-- TODO: Toast notifications handled in Livewire component --}}

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Form Input -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">Parameter</h3>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Tingkat <span class="text-delima">*</span></label>
                    <select wire:model.live="tingkat" class="w-full border rounded px-3 py-2">
                        <option value="7">7</option>
                        <option value="8">8</option>
                        <option value="9">9</option>
                    </select>
                    @error('tingkat') <span class="text-delima text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Jumlah Rombel <span class="text-delima">*</span></label>
                    <input type="number" wire:model.live="jumlah_rombel" min="1" max="10" class="w-full border rounded px-3 py-2">
                    <p class="text-xs text-gray-500 mt-1">Otomatis generate A, B, C, dst</p>
                    @error('jumlah_rombel') <span class="text-delima text-sm">{{ $message }}</span> @enderror
                </div>

            </div>

            <div class="flex gap-2 mt-6">
                <a href="{{ route('admin.master-data.kelas.index') }}" class="px-4 py-2 border rounded hover:bg-gray-50">Batal</a>
                <button wire:click="save" class="px-4 py-2 bg-zamrud text-white rounded hover:bg-zamrud/90 transition-colors">Simpan Semua</button>
            </div>
        </div>

        <!-- Preview -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">Preview ({{ count($preview) }} kelas)</h3>

            @if(count($preview) > 0)
                <div class="space-y-2 max-h-96 overflow-y-auto">
                    @foreach($preview as $item)
                        <div class="border rounded p-3 flex justify-between items-center">
                            <div>
                                <span class="font-semibold text-zamrud">{{ $item['nama_lengkap'] }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center text-gray-500 py-8">Atur parameter untuk melihat preview</div>
            @endif
        </div>
    </div>
</div>
