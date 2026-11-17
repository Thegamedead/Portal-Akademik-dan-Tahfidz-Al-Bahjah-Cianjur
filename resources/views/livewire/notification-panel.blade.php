<div class="notification-panel">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-semibold text-tinta">
            Notifikasi
            @if($unreadCount > 0)
                <span class="ml-2 px-2 py-1 text-xs bg-delima text-white rounded-full">
                    {{ $unreadCount }}
                </span>
            @endif
        </h3>
        @if($unreadCount > 0)
            <button
                wire:click="markAllAsRead"
                class="text-sm text-zamrud hover:text-zamrud-dark transition-colors">
                Tandai Semua Dibaca
            </button>
        @endif
    </div>

    <div class="space-y-2">
        @forelse($notifications as $notification)
            <div
                wire:key="notification-{{ $notification->id }}"
                class="p-4 rounded-lg border transition-colors cursor-pointer
                    {{ $notification->read_at ? 'bg-white border-gray-200' : 'bg-emas-pudar bg-opacity-10 border-emas-pudar' }}"
                wire:click="markAsRead('{{ $notification->id }}')"
            >
                <!-- Notification content based on type -->
                @if($notification->type === 'App\Notifications\UsulanKenaikanTingkatDecisionNotification')
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            @if($notification->data['decision'] === 'approved')
                                <svg class="h-6 w-6 text-zamrud" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            @else
                                <svg class="h-6 w-6 text-delima" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            @endif
                        </div>
                        <div class="ml-3 flex-1">
                            <p class="text-sm font-medium text-tinta">
                                Usulan Kenaikan Tingkat {{ $notification->data['decision_label'] }}
                            </p>
                            <p class="mt-1 text-sm text-gray-600">
                                Usulan kenaikan tingkat untuk <strong>{{ $notification->data['siswa_nama'] }}</strong>
                                ke tingkat <strong>{{ $notification->data['tingkat_tujuan'] }}</strong>
                                telah {{ strtolower($notification->data['decision_label']) }}.
                            </p>
                            @if($notification->data['catatan_keputusan'])
                                <p class="mt-1 text-sm text-gray-500 italic">
                                    Catatan: {{ $notification->data['catatan_keputusan'] }}
                                </p>
                            @endif
                            <p class="mt-1 text-xs text-gray-400">
                                {{ $notification->data['tanggal_keputusan'] }}
                            </p>
                        </div>
                    </div>

                @elseif($notification->type === 'App\Notifications\KenaikanTingkatPlacementNotification')
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-zamrud" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
                            </svg>
                        </div>
                        <div class="ml-3 flex-1">
                            <p class="text-sm font-medium text-tinta">
                                Kenaikan Tingkat Berhasil
                            </p>
                            <p class="mt-1 text-sm text-gray-600">
                                Selamat! Anda telah naik ke tingkat <strong>{{ $notification->data['tingkat_baru'] }}</strong>
                                dan ditempatkan di kelompok <strong>{{ $notification->data['kelompok_nama'] }}</strong>.
                            </p>
                            <p class="mt-1 text-sm text-gray-600">
                                Guru Pembimbing: <strong>{{ $notification->data['guru_pembimbing'] }}</strong>
                            </p>
                            <p class="mt-1 text-xs text-gray-400">
                                {{ $notification->data['tanggal_penempatan'] }}
                            </p>
                        </div>
                    </div>
                @endif

                <!-- Read indicator -->
                @if($notification->unread())
                    <div class="absolute top-2 right-2">
                        <span class="inline-block h-2 w-2 rounded-full bg-zamrud"></span>
                    </div>
                @endif
            </div>
        @empty
            <div class="text-center py-8 text-gray-500">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                </svg>
                <p class="mt-2">Belum ada notifikasi</p>
            </div>
        @endforelse
    </div>
</div>
