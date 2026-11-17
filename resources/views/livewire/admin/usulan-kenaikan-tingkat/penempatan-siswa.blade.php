{{--
    View: livewire/admin/usulan-kenaikan-tingkat/penempatan-siswa.blade.php
    Komponen Livewire: App\Livewire\Admin\UsulanKenaikanTingkat\PenempatanSiswa

    Modal component for admin to place students into target groups.
    Handles both promoted students (from usulan) and new students (from tes penempatan).

    Task 15.1: Implement PenempatanSiswa component (Admin namespace)
    Requirements: 5.4, 6.1, 6.2, 6.3, 12.2, 12.3, 12.4, 12.5
--}}

<div>
    {{-- Modal --}}
    @if($showModal)
        <div
            x-data="{ show: @entangle('showModal') }"
            x-show="show"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 overflow-y-auto pointer-events-none"
            style="font-family:'Inter',sans-serif;"
        >
            {{-- Backdrop --}}
            <div class="fixed inset-0 bg-black/40 pointer-events-auto" wire:click="closeModal"></div>

            {{-- Modal Container --}}
            <div class="flex items-center justify-center min-h-screen px-4 py-6">
                <div
                    x-show="show"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 transform scale-95"
                    x-transition:enter-end="opacity-100 transform scale-100"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 transform scale-100"
                    x-transition:leave-end="opacity-0 transform scale-95"
                    class="relative w-full max-w-4xl bg-white rounded-2xl shadow-xl pointer-events-auto"
                    style="border:1px solid #E5E7EB;"
                >
                    {{-- Modal Header --}}
                    <div class="flex items-center justify-between px-6 py-5 rounded-t-2xl flex-shrink-0" style="background-color:#1B5E4F;">
                        <h3 class="text-lg font-semibold" style="font-family:'Plus Jakarta Sans',sans-serif;color:white;">
                            Penempatan Siswa ke Kelompok
                        </h3>
                        <button
                            type="button"
                            wire:click="closeModal"
                            class="rounded-lg p-1 transition-colors focus:outline-none"
                            style="color:#6B7280;"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    {{-- Modal Body --}}
                    <div class="px-6 py-5 space-y-5">

                        {{-- Placement Error --}}
                        @error('placement')
                            <div class="rounded-lg px-4 py-3 text-sm" style="background-color:#FEE2E2;color:#991B1B;border:1px solid #FCA5A5;">
                                <div class="flex items-start gap-2">
                                    <svg class="w-5 h-5 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span>{{ $message }}</span>
                                </div>
                            </div>
                        @enderror

                        {{-- Student Information Section (Req 5.4, 12.2) --}}
                        <div class="rounded-lg p-4" style="background-color:#EDF7F5;border:1px solid #A7D9CD;">
                            <h4 class="text-sm font-semibold mb-3 flex items-center gap-2" style="font-family:'Plus Jakarta Sans',sans-serif;color:#1B5E4F;">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                Informasi Siswa
                            </h4>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-x-6 gap-y-3 text-sm">
                                {{-- Student Name --}}
                                <div>
                                    <span class="text-xs font-medium" style="color:#065F46;">Nama Siswa</span>
                                    <p class="mt-0.5 font-semibold" style="color:#1A2422;">{{ $studentName }}</p>
                                </div>

                                {{-- NIS --}}
                                <div>
                                    <span class="text-xs font-medium" style="color:#065F46;">NIS</span>
                                    <p class="mt-0.5 font-semibold" style="color:#1A2422;font-variant-numeric:tabular-nums;">{{ $studentNIS }}</p>
                                </div>

                                {{-- Gender --}}
                                <div>
                                    <span class="text-xs font-medium" style="color:#065F46;">Jenis Kelamin</span>
                                    <p class="mt-0.5 font-semibold" style="color:#1A2422;">{{ $studentGender }}</p>
                                </div>

                                {{-- Target Tingkat --}}
                                <div>
                                    <span class="text-xs font-medium" style="color:#065F46;">Tingkat Tujuan</span>
                                    <p class="mt-0.5 font-semibold flex items-center gap-2" style="color:#1B5E4F;">
                                        {{ $targetTingkat }}
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                        </svg>
                                    </p>
                                </div>
                            </div>
                        </div>

                        {{-- Tanggal Penempatan Section --}}
                        <div>
                            <label for="tanggal_penempatan" class="block text-sm font-semibold mb-2" style="font-family:'Plus Jakarta Sans',sans-serif;color:#1A2422;">
                                Tanggal Penempatan <span style="color:#DC2626;">*</span>
                            </label>
                            <input
                                type="date"
                                id="tanggal_penempatan"
                                wire:model="tanggal_penempatan"
                                data-no-flatpickr="true"
                                class="w-full px-3 py-2.5 text-sm rounded-lg focus:outline-none focus:ring-2 focus:ring-[#1B5E4F] transition-all no-flatpickr"
                                style="border:1.5px solid #D1D5DB;color:#1A2422;"
                            />
                            @error('tanggal_penempatan')
                                <p class="mt-1.5 text-xs flex items-center gap-1" style="color:#DC2626;">
                                    <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span>{{ $message }}</span>
                                </p>
                            @enderror
                            <p class="mt-1.5 text-xs" style="color:#6B7280;">
                                <svg class="w-3.5 h-3.5 inline-block mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                </svg>
                                Pilih tanggal penempatan siswa ke kelompok
                            </p>
                        </div>

                        {{-- Available Groups Section (Req 6.1, 6.2, 6.3) --}}
                        <div>
                            <h4 class="text-sm font-semibold mb-3" style="font-family:'Plus Jakarta Sans',sans-serif;color:#1A2422;">
                                Kelompok yang Tersedia
                                <span class="ml-2 text-xs font-normal" style="color:#6B7280;">
                                    ({{ count($availableGroups) }} kelompok tersedia)
                                </span>
                            </h4>

                            @if(count($availableGroups) > 0)
                                {{-- Groups Table --}}
                                <div class="rounded-lg overflow-hidden" style="border:1px solid #E5E7EB;">
                                    <table class="w-full text-sm">
                                        <thead>
                                            <tr style="background-color:#1B5E4F;color:white;border-bottom:1px solid #E5E7EB;">
                                                <th class="px-4 py-3 text-left font-semibold" style="color:white;">Nama Kelompok</th>
                                                <th class="px-4 py-3 text-left font-semibold" style="color:white;">Guru Pembimbing</th>
                                                <th class="px-4 py-3 text-center font-semibold" style="color:white;">Jumlah Siswa</th>
                                                <th class="px-4 py-3 text-center font-semibold" style="color:white;">Kapasitas</th>
                                                <th class="px-4 py-3 text-center font-semibold" style="color:white;">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($availableGroups as $group)
                                                <tr style="border-bottom:1px solid #F3F4F6;" class="hover:bg-gray-50 transition-colors">
                                                    {{-- Group Name --}}
                                                    <td class="px-4 py-3 font-medium" style="color:#1A2422;">
                                                        {{ $group['nama'] }}
                                                    </td>

                                                    {{-- Guru Pembimbing --}}
                                                    <td class="px-4 py-3" style="color:#6B7280;">
                                                        {{ $group['guru_pembimbing'] }}
                                                    </td>

                                                    {{-- Current Count --}}
                                                    <td class="px-4 py-3 text-center font-semibold" style="color:#1A2422;font-variant-numeric:tabular-nums;">
                                                        {{ $group['jumlah_siswa'] }}
                                                    </td>

                                                    {{-- Capacity with Badge --}}
                                                    <td class="px-4 py-3">
                                                        <div class="flex items-center justify-center gap-2">
                                                            <span class="font-semibold" style="color:#1A2422;font-variant-numeric:tabular-nums;">
                                                                {{ $group['kapasitas_maks'] }}
                                                            </span>
                                                            @php
                                                                $badgeColor = $this->getCapacityBadgeColor($group);
                                                            @endphp
                                                            <span
                                                                class="px-2 py-0.5 rounded text-xs font-medium"
                                                                style="background-color:{{ $badgeColor['bg'] }};color:{{ $badgeColor['text'] }};"
                                                            >
                                                                {{ $group['kapasitas_tersisa'] }} tersisa
                                                            </span>
                                                        </div>
                                                    </td>

                                                    {{-- Action Button --}}
                                                    <td class="px-4 py-3 text-center">
                                                        <button
                                                            type="button"
                                                            wire:click="placeToGroup({{ $group['id'] }})"
                                                            wire:loading.attr="disabled"
                                                            wire:target="placeToGroup({{ $group['id'] }})"
                                                            class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed min-w-[90px]"
                                                            style="background-color:#1B5E4F;color:#FFFFFF;border:1px solid #1B5E4F;"
                                                        >
                                                            <span wire:loading.remove wire:target="placeToGroup({{ $group['id'] }})">
                                                                Tempatkan
                                                            </span>
                                                            <span wire:loading.inline-flex wire:target="placeToGroup({{ $group['id'] }})" class="items-center gap-1.5">
                                                                <svg class="animate-spin h-3 w-3 shrink-0" fill="none" viewBox="0 0 24 24">
                                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                                </svg>
                                                                Proses...
                                                            </span>
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                {{-- Capacity Legend --}}
                                <div class="mt-3 flex items-center gap-4 text-xs" style="color:#6B7280;">
                                    <div class="flex items-center gap-1.5">
                                        <div class="w-3 h-3 rounded" style="background-color:#D1FAE5;"></div>
                                        <span>Kapasitas tersedia</span>
                                    </div>
                                    <div class="flex items-center gap-1.5">
                                        <div class="w-3 h-3 rounded" style="background-color:#FEF3C7;"></div>
                                        <span>Hampir penuh (≥80%)</span>
                                    </div>
                                </div>

                            @else
                                {{-- No Groups Available --}}
                                <div class="rounded-lg p-6 text-center" style="background-color:#F9FAFB;border:1px solid #E5E7EB;">
                                    <svg class="w-12 h-12 mx-auto mb-3" style="color:#D1D5DB;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                    </svg>
                                    <p class="text-sm font-medium mb-1" style="color:#6B7280;">
                                        Tidak ada kelompok yang tersedia
                                    </p>
                                    <p class="text-xs" style="color:#9CA3AF;">
                                        Semua kelompok tingkat {{ $targetTingkat }} untuk {{ $studentGender }} sudah penuh.
                                    </p>
                                </div>
                            @endif
                        </div>

                    </div>

                    {{-- Modal Footer --}}
                    <div class="flex items-center justify-end gap-3 px-6 py-4" style="border-top:1px solid #E5E7EB; background-color: #F9FAFB;">
                        {{-- Close Button --}}
                        <button
                            type="button"
                            wire:click="closeModal"
                            class="px-4 py-2.5 text-sm font-medium rounded-lg transition-colors focus:outline-none"
                            style="background-color:#FFFFFF;color:#6B7280;border:1.5px solid #D1D5DB;"
                        >
                            Tutup
                        </button>
                    </div>

                </div>
            </div>
        </div>
    @endif
</div>
