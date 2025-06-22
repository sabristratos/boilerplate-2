<?php

namespace App\Livewire\Admin;

use App\Facades\Settings;
use App\Models\Page;
use Livewire\Component;
use Livewire\WithPagination;

class PageIndex extends Component
{
    use WithPagination;

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
            ->when($this->search, fn ($query) => $query->where('title', 'like', '%' . $this->search . '%'))
            ->when($this->filters['locale'] ?? null, fn ($query, $locale) => $query->where('title->' . $locale, 'like', '%' . $this->search . '%'))
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
        $page = Page::create([
            'title' => 'New Page',
            'slug' => 'new-page-' . uniqid(),
        ]);

        $this->redirectRoute('admin.pages.editor', ['page' => $page]);
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
        Page::findOrFail($this->deleteId)->delete();
        $this->showDeleteModal = false;
        session()->flash('message', 'Page deleted successfully.');
    }

    public function cancelDelete()
    {
        $this->showDeleteModal = false;
        $this->deleteId = null;
    }

    public function render()
    {
        return view('livewire.admin.page-index')
            ->title(__('Pages'));
    }
}
