<?php

namespace Tests\Unit\Components;

use Illuminate\Support\Facades\Blade;
use Tests\TestCase;

class SearchFieldComponentTest extends TestCase
{
    /**
     * Test that search field renders with SVG icon
     */
    public function test_renders_search_field_with_svg_icon(): void
    {
        $view = Blade::render('<x-ui.search-field placeholder="Cari data..." />');

        // Should have SVG icon (not emoji)
        $this->assertStringContainsString('<svg', $view);
        $this->assertStringContainsString('h-5 w-5', $view); // 20x20 icon size
        $this->assertStringContainsString('text-gray-400', $view); // gray-400 color

        // Should NOT have emoji icon
        $this->assertStringNotContainsString('🔍', $view);

        // Should have search icon path
        $this->assertStringContainsString('M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z', $view);
    }

    /**
     * Test that search field has proper padding for icon
     */
    public function test_search_field_has_proper_padding_for_icon(): void
    {
        $view = Blade::render('<x-ui.search-field />');

        // Should have padding-left to accommodate icon
        $this->assertStringContainsString('pl-10', $view);

        // Icon container should be positioned absolutely
        $this->assertStringContainsString('absolute inset-y-0 left-0', $view);
        $this->assertStringContainsString('pointer-events-none', $view);
    }

    /**
     * Test that search field uses design system border color
     */
    public function test_search_field_uses_design_system_border(): void
    {
        $view = Blade::render('<x-ui.search-field />');

        // Should use border #D1D5DB from design system
        $this->assertStringContainsString('border: 1.5px solid #D1D5DB', $view);
    }

    /**
     * Test that search field has Zamrud focus ring
     */
    public function test_search_field_has_zamrud_focus_ring(): void
    {
        $view = Blade::render('<x-ui.search-field />');

        // Should have focus ring in Zamrud color (#1B5E4F)
        $this->assertStringContainsString('focus:ring-[#1B5E4F]', $view);
        $this->assertStringContainsString('focus:border-[#1B5E4F]', $view);
    }

    /**
     * Test that search field accepts custom placeholder
     */
    public function test_search_field_accepts_custom_placeholder(): void
    {
        $view = Blade::render('<x-ui.search-field placeholder="Cari siswa..." />');

        $this->assertStringContainsString('placeholder="Cari siswa..."', $view);
    }

    /**
     * Test that search field supports wire:model for Livewire
     */
    public function test_search_field_supports_wire_model(): void
    {
        $view = Blade::render('<x-ui.search-field wireModel="search" />');

        // Should have wire:model.live.debounce
        $this->assertStringContainsString('wire:model.live.debounce.300ms="search"', $view);
    }

    /**
     * Test that search field works without wire:model (standard form)
     */
    public function test_search_field_works_without_wire_model(): void
    {
        $view = Blade::render('<x-ui.search-field name="query" value="test" />');

        // Should have standard name and value attributes
        $this->assertStringContainsString('name="query"', $view);
        $this->assertStringContainsString('value="test"', $view);

        // Should NOT have wire:model when not provided
        $this->assertStringNotContainsString('wire:model', $view);
    }

    /**
     * Test that search field accepts custom name attribute
     */
    public function test_search_field_accepts_custom_name(): void
    {
        $view = Blade::render('<x-ui.search-field name="student_search" />');

        $this->assertStringContainsString('name="student_search"', $view);
    }

    /**
     * Test that search field is responsive
     */
    public function test_search_field_is_responsive(): void
    {
        $view = Blade::render('<x-ui.search-field />');

        // Should have w-full for full width responsiveness
        $this->assertStringContainsString('w-full', $view);
    }

    /**
     * Test that search field uses Inter font from design system
     */
    public function test_search_field_uses_inter_font(): void
    {
        $view = Blade::render('<x-ui.search-field />');

        // Should use Inter font for body/data (design system)
        // Note: Blade may encode quotes as HTML entities
        $this->assertTrue(
            str_contains($view, "font-family: 'Inter', sans-serif") ||
            str_contains($view, "font-family: &#039;Inter&#039;, sans-serif"),
            'Search field should use Inter font from design system'
        );
    }

    /**
     * Test that search field accepts additional CSS classes
     */
    public function test_search_field_accepts_additional_classes(): void
    {
        $view = Blade::render('<x-ui.search-field class="md:w-1/2 mb-4" />');

        // Custom classes should be merged with base classes
        $this->assertStringContainsString('md:w-1/2', $view);
        $this->assertStringContainsString('mb-4', $view);

        // Base classes should still be present
        $this->assertStringContainsString('w-full', $view);
        $this->assertStringContainsString('rounded-lg', $view);
    }

    /**
     * Test that search field type is "search"
     */
    public function test_search_field_type_is_search(): void
    {
        $view = Blade::render('<x-ui.search-field />');

        $this->assertStringContainsString('type="search"', $view);
    }

    /**
     * Test that search field has text color from design system
     */
    public function test_search_field_uses_design_system_text_color(): void
    {
        $view = Blade::render('<x-ui.search-field />');

        // Should use Tinta (#1A2422) for text color
        $this->assertStringContainsString('color: #1A2422', $view);

        // Should have white background
        $this->assertStringContainsString('background-color: #FFFFFF', $view);
    }
}
