<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class NavButton extends Component
{
    public function __construct(
        public string $label,
        public string $route,
        public bool $active = false
    ) {}

    public function render(): View
    {
        return view('components.nav-button');
    }
}