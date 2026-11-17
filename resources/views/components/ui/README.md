# UI Components

## Button Component

Reusable button component that follows the design system with consistent colors, cursor pointer, and hover effects.

### Location
`resources/views/components/ui/button.blade.php`

### Usage

```blade
<!-- Primary button (Zamrud #1B5E4F) -->
<x-ui.button variant="primary">Save</x-ui.button>

<!-- Danger button (Delima #9B3A3A) -->
<x-ui.button variant="danger">Delete</x-ui.button>

<!-- Secondary button (Biru Slate #5B7A8C) -->
<x-ui.button variant="secondary">Cancel</x-ui.button>

<!-- Outline button -->
<x-ui.button variant="outline">View Details</x-ui.button>

<!-- Submit button -->
<x-ui.button variant="primary" type="submit">Submit Form</x-ui.button>

<!-- Disabled button -->
<x-ui.button variant="primary" :disabled="true">Disabled</x-ui.button>

<!-- With Livewire -->
<x-ui.button variant="primary" wire:click="save">Save</x-ui.button>

<!-- With custom classes -->
<x-ui.button variant="primary" class="w-full">Full Width Button</x-ui.button>

<!-- With wire:loading -->
<x-ui.button variant="primary" wire:click="save" wire:loading.attr="disabled">
    Save
</x-ui.button>
```

### Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `variant` | string | `'primary'` | Button variant: `primary`, `danger`, `secondary`, or `outline` |
| `type` | string | `'button'` | HTML button type: `button`, `submit`, or `reset` |
| `disabled` | boolean | `false` | Whether the button is disabled |
| `class` | string | `''` | Additional CSS classes to apply |

### Features

- **Cursor Pointer**: Always shows pointer cursor on non-disabled buttons
- **Hover Effects**: Background color darkens on hover
- **Focus Ring**: Shows focus ring for accessibility (keyboard navigation)
- **Transition**: Smooth color transitions between states
- **Disabled State**: Reduced opacity and no-drop cursor when disabled
- **Design System Colors**:
  - Primary: Zamrud (#1B5E4F) with hover (#164433)
  - Danger: Delima (#9B3A3A) with hover (#7a2e2e)
  - Secondary: Biru Slate (#5B7A8C) with hover (#4a5f6d)
  - Outline: White background with Zamrud border

### Testing

Visual test available at: http://127.0.0.1:8000/test-button-component

Unit tests: `tests/Unit/Components/ButtonComponentTest.php`

Run tests: `vendor/bin/phpunit tests/Unit/Components/ButtonComponentTest.php`

### Requirements

Validates requirements:
- 2.1.1: Consistent button interactions on Sekolah page
- 2.1.2: Consistent button interactions on Kelas page
- 2.1.3: Consistent button interactions on form pages
- 2.1.4: Primary actions use Zamrud color
- 2.1.5: Danger actions use Delima color
- 2.1.6: Secondary actions use Biru Slate color

---

## Modal Component

Reusable modal/dialog component that follows the design system with Tinta background overlay and consistent button colors.

### Location
`resources/views/components/ui/modal.blade.php`

### Usage

```blade
<!-- Basic confirmation modal with Livewire -->
<x-ui.modal
    wire:model="showDeleteModal"
    title="Konfirmasi Hapus"
    confirmText="Hapus"
    cancelText="Batal"
    confirmVariant="danger"
    wire:click="deleteItem"
>
    <p>Apakah Anda yakin ingin menghapus data ini?</p>
    <p class="text-sm text-gray-600 mt-2">Tindakan ini tidak dapat dibatalkan.</p>
</x-ui.modal>

<!-- Primary action modal (e.g., Save, Submit) -->
<x-ui.modal
    wire:model="showSaveModal"
    title="Konfirmasi Simpan"
    confirmText="Simpan"
    cancelText="Batal"
    confirmVariant="primary"
    wire:click="save"
>
    <p>Apakah Anda yakin ingin menyimpan perubahan?</p>
</x-ui.modal>

<!-- Simple modal without title -->
<x-ui.modal
    wire:model="showSimpleModal"
    confirmText="OK"
    cancelText="Tutup"
    wire:click="confirm"
>
    <p>Operasi berhasil dilakukan.</p>
</x-ui.modal>
```

### Livewire Component Setup

In your Livewire component PHP class:

```php
class IndexKelas extends Component
{
    public bool $showDeleteModal = false;
    public $itemToDelete;

    public function confirmDelete($id)
    {
        $this->itemToDelete = $id;
        $this->showDeleteModal = true;
    }

    public function deleteItem()
    {
        // Delete logic here
        $this->showDeleteModal = false;
        $this->dispatch('toast', type: 'success', message: 'Data berhasil dihapus');
    }
}
```

In your Blade view:

```blade
<x-ui.button 
    variant="danger" 
    wire:click="confirmDelete({{ $item->id }})">
    Hapus
</x-ui.button>

<x-ui.modal
    wire:model="showDeleteModal"
    title="Konfirmasi Hapus"
    confirmText="Hapus"
    cancelText="Batal"
    confirmVariant="danger"
    wire:click="deleteItem"
>
    <p>Apakah Anda yakin ingin menghapus data ini?</p>
</x-ui.modal>
```

### Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `title` | string | `''` | Modal title (optional, if empty no header shown) |
| `confirmText` | string | `'Konfirmasi'` | Text for the confirm button |
| `cancelText` | string | `'Batal'` | Text for the cancel button |
| `confirmVariant` | string | `'primary'` | Button variant for confirm button: `primary` or `danger` |
| `show` | boolean | `false` | Initial show state (use with wire:model) |
| `wire:model` | string | required | Livewire property to control modal visibility |
| `wire:click` | string | optional | Livewire method to call when confirm button is clicked |

### Features

- **Design System Backdrop**: Uses Tinta color (#1A2422) with 60% opacity as specified in design system
- **Alpine.js Integration**: Supports `x-show`, `x-transition` for smooth animations
- **Keyboard Support**: Press `Escape` to close modal
- **Click Outside**: Click backdrop to close modal
- **Responsive Design**: Mobile-first responsive layout
  - Mobile: Full-width buttons stacked vertically
  - Tablet/Desktop: Buttons inline horizontally
- **Button Integration**: Uses `<x-ui.button>` component internally
  - Cancel button: Uses `secondary` variant (Biru Slate)
  - Confirm button: Uses variant specified in `confirmVariant` prop
- **Centered & Accessible**: Modal content is centered with proper ARIA support
- **Smooth Transitions**: Fade and scale animations for enter/leave

### Design System Colors

- **Backdrop**: Tinta (#1A2422) with 60% opacity (rgba(26, 36, 34, 0.6))
- **Confirm Button (primary)**: Zamrud (#1B5E4F)
- **Confirm Button (danger)**: Delima (#9B3A3A)
- **Cancel Button**: Biru Slate (#5B7A8C)
- **Title Text**: Tinta (#1A2422) with Plus Jakarta Sans font
- **Body Text**: Gray-700 with Inter font

### Requirements

Validates requirements:
- 2.3.1: Modal background uses Tinta rgba(26, 36, 34, 0.6) overlay
- 2.3.2: Primary action modals use Zamrud color for confirm button
- 2.3.3: Danger action modals (delete) use Delima color for confirm button
- 2.3.4: Cancel buttons use Biru Slate color for distinction

---

## Search Field Component

Reusable search input component with consistent SVG icon following the design system.

### Location
`resources/views/components/ui/search-field.blade.php`

### Usage

```blade
<!-- Basic search with Livewire -->
<x-ui.search-field 
    wireModel="search"
    placeholder="Cari nama atau NIS siswa..."
/>

<!-- Search for teachers -->
<x-ui.search-field 
    wireModel="search"
    placeholder="Cari guru (NIY, Nama)..."
/>

<!-- Search for classes -->
<x-ui.search-field 
    wireModel="search"
    placeholder="Cari nama kelas atau wali kelas..."
/>

<!-- Standard form (without Livewire) -->
<form method="GET" action="{{ route('search') }}">
    <x-ui.search-field 
        name="query"
        placeholder="Cari..."
        value="{{ request('query') }}"
    />
    <button type="submit">Search</button>
</form>

<!-- With custom width -->
<x-ui.search-field 
    wireModel="search"
    placeholder="Cari data..."
    class="md:w-1/2 lg:w-1/3"
/>

<!-- With wire:loading indicator -->
<div>
    <x-ui.search-field 
        wireModel="searchTerm"
        placeholder="Cari siswa..."
    />
    
    <div wire:loading wire:target="searchTerm">
        Mencari...
    </div>
</div>
```

### Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `placeholder` | string | `'Cari...'` | Placeholder text for the input |
| `wireModel` | string | `null` | Livewire model name (adds wire:model.live.debounce.300ms automatically) |
| `name` | string | `'search'` | HTML name attribute for standard forms |
| `value` | string | `''` | Initial value (for non-Livewire forms) |
| `class` | string | `''` | Additional CSS classes to merge |

### Features

- **SVG Icon**: Magnifying glass icon (20x20, gray-400 color) on the left - NO emoji
- **Design System Compliance**:
  - Border: #D1D5DB (neutral gray)
  - Focus Ring: #1B5E4F (Zamrud - primary brand color)
  - Text Color: #1A2422 (Tinta - main text color)
  - Typography: Inter font (for body/data)
- **Proper Padding**: Input has `pl-10` to accommodate the icon
- **Responsive**: Full width by default, works on mobile and desktop
- **Livewire Support**: Automatic debouncing (300ms) when using `wireModel`
- **Standard Forms**: Works without Livewire using standard `name` and `value` attributes
- **Smooth Transitions**: Focus transitions for better UX
- **Accessibility**: Proper `type="search"` attribute

### Before vs After

**Before (Inconsistent - WRONG)**:
```blade
<!-- ❌ Using emoji -->
<input type="text" 
    wire:model.live="search" 
    placeholder="🔍 Cari siswa..." 
    class="w-full border rounded px-3 py-2"
/>

<!-- ❌ No icon -->
<input type="search" 
    wire:model.live.debounce.300ms="search" 
    placeholder="Cari guru..."
/>
```

**After (Consistent - CORRECT)**:
```blade
<!-- ✅ Consistent SVG icon -->
<x-ui.search-field 
    wireModel="search"
    placeholder="Cari siswa..."
/>
```

### Testing

Unit tests: `tests/Unit/Components/SearchFieldComponentTest.php`

Run tests: `php artisan test --filter=SearchFieldComponentTest`

Demo page: `resources/views/components/ui/search-field-demo.blade.php`

Usage examples: `resources/views/components/ui/search-field-usage-examples.md`

### Requirements

Validates requirements:
- 2.4.1: Search fields have consistent SVG icon (not emoji)
- 2.4.2: Border uses #D1D5DB with Zamrud focus ring
- 2.4.3: Component is responsive (works on mobile and desktop)

### Bug Fixed

**Bug Condition**: Search fields were inconsistent - some had emoji 🔍, some had no icon

**Expected Behavior**: All search fields have consistent SVG icon and styling

**Preservation**: Search filtering logic and wire:model bindings remain unchanged
