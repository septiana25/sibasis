<?php

namespace App\View\Components;

use Illuminate\View\Component;

class SkripsiItem extends Component
{
    public array $skripsi;
    public int $index;
    public array $fakultasList;
    public array $prodiList;
    public string|null $selectedFakultas;
    public string|null $selectedProdi;

    /**
     * Create a new component instance.
     */
    public function __construct(
        array $skripsi,
        int $index,
        array $fakultasList = [],
        array $prodiList = [],
        string|null $selectedFakultas = null,
        string|null $selectedProdi = null
    ) {
        $this->skripsi = $skripsi;
        $this->index = $index;
        $this->fakultasList = $fakultasList;
        $this->prodiList = $prodiList;
        $this->selectedFakultas = $selectedFakultas;
        $this->selectedProdi = $selectedProdi;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render()
    {
        return view('components.skripsi-item');
    }
}
