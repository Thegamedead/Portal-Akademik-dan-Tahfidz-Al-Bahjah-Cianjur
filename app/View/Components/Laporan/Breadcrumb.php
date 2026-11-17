<?php

namespace App\View\Components\Laporan;

use Illuminate\View\Component;

/**
 * Breadcrumb component for Laporan module.
 * Supports Requirement 12.5: Breadcrumb navigation.
 *
 * Usage: <x-laporan.breadcrumb :items="[['label'=>'Home','url'=>'/'], ['label'=>'Laporan','url'=>route('laporan.dashboard')], ['label'=>'Progres Hafalan']]" />
 */
class Breadcrumb extends Component
{
    /**
     * @param array $items  Array of ['label' => string, 'url' => string|null]
     *                      Last item has no 'url' (current page).
     */
    public function __construct(public array $items = []) {}

    public function render()
    {
        return view('components.laporan.breadcrumb');
    }
}
