<?php

namespace App\Livewire;

use App\Models\Page;
use App\Models\Testimonial;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app', ['title' => 'Dashboard'])]
class Dashboard extends Component
{
    public function render()
    {
        $stats = [
            [
                'name' => 'Total Pages',
                'value' => Page::count(),
                'icon' => 'book-open-text',
            ],
            [
                'name' => 'Total Users',
                'value' => User::count(),
                'icon' => 'users',
            ],
            [
                'name' => __('dashboard.testimonials'),
                'value' => Testimonial::count(),
                'icon' => 'chat-bubble-left-right',
            ],
        ];

        return view('livewire.dashboard', [
            'stats' => $stats,
        ]);
    }
}
