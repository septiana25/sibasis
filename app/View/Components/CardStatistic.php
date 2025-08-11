<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class CardStatistic extends Component
{
    public string $title;
    public array $icons;
    public string $total;

    public function __construct(string $title, array $icons, string $total)
    {
        $this->title = $title;
        $this->icons = $icons;
        $this->total = $total;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.card-statistic');
    }
}
