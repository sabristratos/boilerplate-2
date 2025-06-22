<?php

namespace App\Livewire\Admin;

use App\Models\Page;
use Illuminate\Support\Str;
use Livewire\Component;

class PageCreate extends Component
{
    public array $title = [];
    public array $slug = [];
    public array $availableLocales = [];

    public function mount(): void
    {
        $this->availableLocales = config('app.available_locales', ['en' => 'English']);
        foreach ($this->availableLocales as $locale => $language) {
            $this->title[$locale] = '';
            $this->slug[$locale] = '';
        }
    }

    public function generateSlug(string $locale): void
    {
        if (isset($this->title[$locale])) {
            $this->slug[$locale] = Str::slug($this->title[$locale]);
        }
    }

    public function save(): void
    {
        $this->validate([
            'title.en' => 'required|string|max:255',
            'slug.en' => 'required|string|max:255|unique:pages,slug->en',
        ]);

        $page = Page::create([
            'title' => $this->title,
            'slug' => $this->slug,
        ]);

        $this->redirectRoute('admin.pages.editor', ['page' => $page]);
    }

    public function render()
    {
        return view('livewire.admin.page-create')
            ->layout('components.layouts.app');
    }
}
