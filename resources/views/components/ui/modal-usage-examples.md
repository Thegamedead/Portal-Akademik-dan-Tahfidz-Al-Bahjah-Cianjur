# Modal Component Usage Examples

The reusable Modal component (`<x-ui.modal>`) follows the design system with Tinta background overlay and design system button colors.

## Props

- `title` - Modal title (optional)
- `confirmText` - Confirm button text (default: 'Konfirmasi')
- `cancelText` - Cancel button text (default: 'Batal')
- `confirmVariant` - Confirm button variant: 'primary', 'danger', 'secondary' (default: 'primary')
- `show` - Initial show state (default: false)
- `wireModel` - Livewire property to entangle with (optional)

## Usage Examples

### Basic Usage with Livewire (Recommended)

```blade
{{-- In your Livewire view --}}
@if($showModal)
    <x-ui.modal
        title="Konfirmasi Hapus"
        confirmText="Hapus"
        cancelText="Batal"
        confirmVariant="danger"
        wireModel="showModal"
        wire:click="confirmDelete"
    >
        <p>Apakah Anda yakin ingin menghapus data ini?</p>
    </x-ui.modal>
@endif
```

### Custom Footer

```blade
@if($showModal)
    <x-ui.modal
        title="Custom Actions"
        wireModel="showModal"
    >
        <p>Modal content here</p>
        
        <x-slot:footer>
            <x-ui.button variant="secondary" @click="show = false">
                Tutup
            </x-ui.button>
            <x-ui.button variant="primary" wire:click="saveChanges">
                Simpan
            </x-ui.button>
            <x-ui.button variant="danger" wire:click="deleteItem">
                Hapus
            </x-ui.button>
        </x-slot:footer>
    </x-ui.modal>
@endif
```

### Simple Confirmation Modal

```blade
@if($showDeleteConfirmation)
    <x-ui.modal
        title="Hapus Kelas"
        confirmText="Ya, Hapus"
        cancelText="Batal"
        confirmVariant="danger"
        wireModel="showDeleteConfirmation"
        wire:click="deleteKelas"
    >
        <p class="text-gray-700">
            Apakah Anda yakin ingin menghapus kelas <strong>{{ $kelasToDelete->nama }}</strong>?
        </p>
        <p class="text-sm text-gray-500 mt-2">
            Tindakan ini tidak dapat dibatalkan.
        </p>
    </x-ui.modal>
@endif
```

### Bulk Delete Modal

```blade
@if($showBulkDeleteModal)
    <x-ui.modal
        title="Hapus {{ count($selectedIds) }} Item"
        confirmText="Hapus Semua"
        cancelText="Batal"
        confirmVariant="danger"
        wireModel="showBulkDeleteModal"
        wire:click="bulkDelete"
    >
        <p>Anda akan menghapus <strong>{{ count($selectedIds) }} item</strong> yang dipilih.</p>
        <p class="text-sm text-red-600 mt-2">Peringatan: Tindakan ini tidak dapat dibatalkan!</p>
    </x-ui.modal>
@endif
```

## Design System Compliance

✅ **Backdrop**: Uses `bg-[#1A2422] bg-opacity-60` (Tinta with 60% opacity)
✅ **Buttons**: Uses `<x-ui.button>` component with design system colors
  - `primary` → Zamrud (#1B5E4F)
  - `danger` → Delima (#9B3A3A)
  - `secondary` → Biru Slate (#5B7A8C)
✅ **Typography**: Plus Jakarta Sans for title
✅ **Transitions**: Smooth fade and scale animations
✅ **Responsive**: Works on mobile, tablet, and desktop

## Livewire Component Setup

```php
class IndexKelas extends Component
{
    public $showModal = false;
    public $kelasToDelete = null;
    
    public function confirmDelete($id)
    {
        $this->kelasToDelete = Kelas::find($id);
        $this->showModal = true;
    }
    
    public function deleteKelas()
    {
        $this->kelasToDelete->delete();
        $this->showModal = false;
        $this->dispatch('toast', type: 'success', message: 'Kelas berhasil dihapus');
    }
}
```

## Migration from Old Modal Pattern

### Before (Old Pattern)

```blade
@if($showModal)
    <div class="fixed inset-0 z-50 overflow-y-auto">
        <div class="fixed inset-0 bg-black bg-opacity-50"></div>
        <div class="flex items-center justify-center min-h-screen">
            <div class="bg-white rounded-lg p-6">
                <h3>{{ $title }}</h3>
                <div>{{ $content }}</div>
                <button wire:click="$set('showModal', false)">Batal</button>
                <button wire:click="confirm">OK</button>
            </div>
        </div>
    </div>
@endif
```

### After (New Component)

```blade
@if($showModal)
    <x-ui.modal
        title="{{ $title }}"
        wireModel="showModal"
        wire:click="confirm"
    >
        {{ $content }}
    </x-ui.modal>
@endif
```

## Benefits

1. **Consistent Design**: All modals use the same Tinta background and button colors
2. **Less Code**: No need to duplicate modal structure in every page
3. **Maintainable**: Update modal styling in one place
4. **Accessible**: Built-in Alpine.js transitions and click-away handling
5. **Flexible**: Support for custom footers and content
