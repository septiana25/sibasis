<?php

namespace App\View\Components;

use Illuminate\View\Component;

class PostPagination extends Component
{
    public array $results;
    public string $route;
    public array $params;

    public function __construct(array $results, string $route, array $params = [])
    {
        $this->results = $results;
        $this->route = $route;
        $this->params = $params;
    }

    public function render()
    {
        return view('components.post-pagination');
    }
}
