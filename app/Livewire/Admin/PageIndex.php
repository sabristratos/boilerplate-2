<?php

namespace App\Livewire\Admin;

use App\Facades\Settings;
use App\Models\Page;
use App\Traits\WithToastNotifications;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Str;

class PageIndex extends Component
{
    use WithPagination, WithToastNotifications;

    public string $search = '';
    public int $perPage = 10;
    public string $sortBy = 'title';
    public string $sortDirection = 'asc';
    public array $filters = [];
    public bool $showFiltersModal = false;
    public $showDeleteModal = false;
    public $deleteId = null;

    public function getPagesProperty()
    {
        return Page::query()
            ->when($this->search, function ($query, $search) {
                $locale = $this->filters['locale'] ?? app()->getLocale();
                $query->where(fn($q) => $q->where('slug', 'like', '%' . $search . '%')
                    ->orWhereTranslation('title', 'like', '%' . $search . '%', $locale)
                );
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);
    }

    public function getLocalesProperty()
    {
        $localesSetting = Settings::get('general.available_locales', []);
        return collect($localesSetting)->pluck('name', 'code')->all();
    }

    public function sort(string $column)
    {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'asc';
        }
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    public function createPage()
    {
        $defaultLocale = config('app.fallback_locale');
        $title = __('messages.page_index.new_page_title');

        $page = Page::create([
            'title' => [
                $defaultLocale => $title,
            ],
            'slug' => Str::slug($title) . '-' . uniqid(),
        ]);

        $this->redirectRoute('admin.pages.editor', ['page' => $page->id, 'locale' => $defaultLocale]);
    }

    public function resetFilters()
    {
        $this->filters = [];
    }

    public function confirmDelete(int $id)
    {
        $this->deleteId = $id;
        $this->showDeleteModal = true;
    }

    public function delete()
    {
        Page::find($this->deleteId)?->delete();

        $this->showDeleteModal = false;
        $this->showSuccessToast(__('Page deleted successfully.'));
    }

    public function cancelDelete()
    {
        $this->showDeleteModal = false;
        $this->deleteId = null;
    }

    public function render()
    {
        return view('livewire.admin.page-index')
            ->title(__('messages.page_index.title'));
    }
}
