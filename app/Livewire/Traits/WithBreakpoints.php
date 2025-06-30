<?php

namespace App\Livewire\Traits;

use Livewire\Attributes\On;

trait WithBreakpoints
{
    public string $breakpoint = 'lg';

    #[On('breakpoint-updated')]
    public function updateBreakpoint(string $breakpoint): void
    {
        $this->breakpoint = $breakpoint;
    }

    public function getIsMobileProperty(): bool
    {
        return in_array($this->breakpoint, ['xs', 'sm']);
    }

    public function getIsTabletProperty(): bool
    {
        return in_array($this->breakpoint, ['md', 'lg']);
    }

    public function getIsDesktopProperty(): bool
    {
        return in_array($this->breakpoint, ['xl', '2xl']);
    }
}
