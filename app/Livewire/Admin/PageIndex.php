<?php

namespace App\Livewire\Admin;

use App\Models\Page;
use Livewire\Component;

class PageIndex extends Component
{
    public function getPagesProperty()
    {
        return Page::all();
    }

    public function render()
    {
        return view('livewire.admin.page-index')
            ->title(__('Pages'));
    }
}
