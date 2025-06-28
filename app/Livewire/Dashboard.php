<?php

namespace App\Livewire;

use App\Models\FormSubmission;
use App\Models\Page;
use App\Models\Testimonial;
use App\Models\User;
use Livewire\Component;

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
                'name' => 'Form Submissions',
                'value' => FormSubmission::count(),
                'icon' => 'inbox-stack',
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

        $recentSubmissions = FormSubmission::with('form')
            ->latest()
            ->take(5)
            ->get();

        return view('livewire.dashboard', [
            'stats' => $stats,
            'recentSubmissions' => $recentSubmissions,
        ]);
    }
}
