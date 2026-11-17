# Search Field Component Usage Examples

The `<x-ui.search-field>` component provides a consistent search input with SVG icon following the design system.

## Features

- ✅ SVG magnifying glass icon (20x20, gray-400) - NO emoji
- ✅ Border #D1D5DB with Zamrud (#1B5E4F) focus ring
- ✅ Inter font for text (design system)
- ✅ Responsive (works on mobile and desktop)
- ✅ Supports both Livewire (wire:model) and standard forms
- ✅ Proper padding to accommodate icon

## Basic Usage

### With Livewire (wire:model)

```blade
<x-ui.search-field 
    wireModel="search"
    placeholder="Cari nama atau NIS..."
/>
```

This will generate:
- `wire:model.live.debounce.300ms="search"` - automatic debouncing
- Live search updates as user types (with 300ms delay)

### Standard Form (without Livewire)

```blade
<x-ui.search-field 
    name="query"
    placeholder="Cari guru..."
    value="{{ request('query') }}"
/>
```

Use with standard form submission.

## Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `placeholder` | string | `'Cari...'` | Placeholder text |
| `wireModel` | string | `null` | Livewire model name (adds wire:model.live.debounce.300ms) |
| `name` | string | `'search'` | Input name attribute |
| `value` | string | `''` | Initial value (for non-Livewire forms) |
| `class` | string | `''` | Additional CSS classes |

## Examples

### 1. Search Students (Admin/Guru page)

```blade
<x-ui.search-field 
    wireModel="search"
    placeholder="Cari siswa (NIS, NISN, Nama)..."
/>
```

### 2. Search Teachers

```blade
<x-ui.search-field 
    wireModel="search"
    placeholder="Cari guru (NIY, Nama)..."
/>
```

### 3. Search Classes

```blade
<x-ui.search-field 
    wireModel="search"
    placeholder="Cari nama kelas atau wali kelas..."
/>
```

### 4. With Custom Width

```blade
<x-ui.search-field 
    wireModel="search"
    placeholder="Cari data..."
    class="md:w-1/2 lg:w-1/3"
/>
```

The component already has `w-full` by default, so additional responsive classes will be merged.

### 5. Standard Form Search

```blade
<form method="GET" action="{{ route('search') }}">
    <x-ui.search-field 
        name="q"
        placeholder="Cari..."
        value="{{ request('q') }}"
    />
    <button type="submit">Search</button>
</form>
```

### 6. With Wire Loading State

```blade
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

## Migration from Old Patterns

### Before (Emoji Icon - WRONG)

```blade
<!-- ❌ DON'T USE - Inconsistent emoji -->
<input type="text" 
    wire:model.live="search" 
    placeholder="🔍 Cari siswa..." 
    class="w-full border rounded px-3 py-2"
/>
```

### After (SVG Icon - CORRECT)

```blade
<!-- ✅ USE - Consistent SVG icon -->
<x-ui.search-field 
    wireModel="search"
    placeholder="Cari siswa..."
/>
```

### Before (No Icon - WRONG)

```blade
<!-- ❌ DON'T USE - No visual indicator -->
<input type="search" 
    wire:model.live.debounce.300ms="cari" 
    placeholder="Cari username admin…"
/>
```

### After (With Icon - CORRECT)

```blade
<!-- ✅ USE - Consistent with icon -->
<x-ui.search-field 
    wireModel="cari"
    placeholder="Cari username admin..."
/>
```

## Design System Compliance

This component follows the design system rules from `.kiro/steering/ui-design-system.md`:

- **Border**: `#D1D5DB` (neutral gray)
- **Focus Ring**: `#1B5E4F` (Zamrud - primary brand color)
- **Text Color**: `#1A2422` (Tinta - main text color)
- **Icon Color**: `gray-400` (subtle, not dominant)
- **Typography**: Inter font (for body/data)
- **Icon**: SVG magnifying glass 20x20 (not emoji)

## Requirements Validated

This component validates:
- **Requirement 2.4.1**: Search fields have consistent SVG icon
- **Requirement 2.4.2**: Border uses #D1D5DB with Zamrud focus ring
- **Requirement 2.4.3**: Component is responsive (mobile and desktop)

## Bug Fixed

**Bug Condition**: Search fields were inconsistent - some had emoji 🔍, some had no icon

**Expected Behavior**: All search fields have consistent SVG icon and styling

**Solution**: This reusable component ensures all search fields follow the same pattern
