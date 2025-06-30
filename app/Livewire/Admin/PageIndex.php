<?php

namespace App\Livewire\Admin;

use App\Facades\Settings;
use App\Models\Page;
use App\Traits\WithToastNotifications;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithPagination;

class PageIndex extends Component
{
    use WithPagination, WithToastNotifications;

    public string $search = '';

    public int $perPage = 10;

    public string $sortBy = 'title';

    public string $sortDirection = 'asc';

    public array $filters = [];

    public bool $showFiltersPopover = false;

    public $showDeleteModal = false;

    public $deleteId;

    public function getPagesProperty()
    {
        return Page::query()
            ->when($this->search, function ($query, $search): void {
                $locale = $this->filters['locale'] ?? app()->getLocale();
                $query->where(fn ($q) => $q->where('slug', 'like', '%'.$search.'%')
                    ->orWhereRaw("JSON_EXTRACT(title, '$.\"{$locale}\"') LIKE ?", ["%{$search}%"])
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

    public function sort(string $column): void
    {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'asc';
        }
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedPerPage(): void
    {
        $this->resetPage();
    }

    public function createPage(): void
    {
        $defaultLocale = config('app.fallback_locale');
        $title = __('messages.page_index.new_page_title');

        $page = Page::create([
            'title' => [
                $defaultLocale => $title,
            ],
            'slug' => Str::slug($title).'-'.uniqid(),
        ]);

        $this->redirectRoute('admin.pages.editor', ['page' => $page->id, 'locale' => $defaultLocale]);
    }

    public function resetFilters(): void
    {
        $this->filters = [];
        $this->showFiltersPopover = false;
    }

    public function confirmDelete(int $id): void
    {
        $this->deleteId = $id;
        $this->showDeleteModal = true;
    }

    public function delete(): void
    {
        Page::find($this->deleteId)?->delete();

        $this->showDeleteModal = false;
        $this->showSuccessToast(__('Page deleted successfully.'));
    }

    public function cancelDelete(): void
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
