<?php

namespace Tests\Unit\Components;

use Illuminate\Support\Facades\Blade;
use Tests\TestCase;

class ButtonComponentTest extends TestCase
{
    public function test_renders_primary_button_with_correct_classes(): void
    {
        $view = Blade::render('<x-ui.button variant="primary">Save</x-ui.button>');

        $this->assertStringContainsString('cursor-pointer', $view);
        $this->assertStringContainsString('transition-colors', $view);
        $this->assertStringContainsString('focus:outline-none', $view);
        $this->assertStringContainsString('focus:ring-2', $view);
        $this->assertStringContainsString('bg-[#1B5E4F]', $view); // Zamrud
        $this->assertStringContainsString('hover:bg-[#164433]', $view); // Zamrud hover
        $this->assertStringContainsString('Save', $view);
    }

    public function test_renders_danger_button_with_correct_classes(): void
    {
        $view = Blade::render('<x-ui.button variant="danger">Delete</x-ui.button>');

        $this->assertStringContainsString('cursor-pointer', $view);
        $this->assertStringContainsString('bg-[#9B3A3A]', $view); // Delima
        $this->assertStringContainsString('hover:bg-[#7a2e2e]', $view); // Delima hover
        $this->assertStringContainsString('Delete', $view);
    }

    public function test_renders_secondary_button_with_correct_classes(): void
    {
        $view = Blade::render('<x-ui.button variant="secondary">Cancel</x-ui.button>');

        $this->assertStringContainsString('cursor-pointer', $view);
        $this->assertStringContainsString('bg-[#5B7A8C]', $view); // Biru Slate
        $this->assertStringContainsString('hover:bg-[#4a5f6d]', $view); // Biru Slate hover
        $this->assertStringContainsString('Cancel', $view);
    }

    public function test_renders_outline_button_with_correct_classes(): void
    {
        $view = Blade::render('<x-ui.button variant="outline">View Details</x-ui.button>');

        $this->assertStringContainsString('cursor-pointer', $view);
        $this->assertStringContainsString('bg-white', $view);
        $this->assertStringContainsString('border-2', $view);
        $this->assertStringContainsString('border-[#1B5E4F]', $view); // Zamrud border
        $this->assertStringContainsString('hover:bg-gray-50', $view);
        $this->assertStringContainsString('View Details', $view);
    }

    public function test_applies_disabled_state_correctly(): void
    {
        $view = Blade::render('<x-ui.button :disabled="true">Disabled</x-ui.button>');

        $this->assertStringContainsString('disabled', $view);
        $this->assertStringContainsString('opacity-50', $view);
        $this->assertStringContainsString('cursor-not-allowed', $view);
    }

    public function test_supports_submit_type(): void
    {
        $view = Blade::render('<x-ui.button type="submit">Submit</x-ui.button>');

        $this->assertStringContainsString('type="submit"', $view);
        $this->assertStringContainsString('Submit', $view);
    }

    public function test_accepts_additional_classes(): void
    {
        $view = Blade::render('<x-ui.button class="w-full">Full Width</x-ui.button>');

        $this->assertStringContainsString('w-full', $view);
        $this->assertStringContainsString('cursor-pointer', $view);
    }

    public function test_supports_wire_attributes(): void
    {
        $view = Blade::render('<x-ui.button wire:click="save">Save</x-ui.button>');

        $this->assertStringContainsString('wire:click="save"', $view);
    }

    public function test_defaults_to_primary_variant_when_not_specified(): void
    {
        $view = Blade::render('<x-ui.button>Default Button</x-ui.button>');

        $this->assertStringContainsString('bg-[#1B5E4F]', $view); // Should be Zamrud (primary)
        $this->assertStringContainsString('cursor-pointer', $view);
    }

    public function test_defaults_to_button_type_when_not_specified(): void
    {
        $view = Blade::render('<x-ui.button>Click Me</x-ui.button>');

        $this->assertStringContainsString('type="button"', $view);
    }
}
