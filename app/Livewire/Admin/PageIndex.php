<?php

declare(strict_types=1);

namespace App\Livewire\Admin;

use App\DTOs\PageDTO;
use App\Facades\Settings;
use App\Models\Page;
use App\Services\PageService;
use App\Traits\WithToastNotifications;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
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

    /**
     * Page service instance.
     */
    protected PageService $pageService;

    /**
     * Boot the component and inject dependencies.
     */
    public function boot(PageService $pageService): void
    {
        $this->pageService = $pageService;
    }

    public function getPagesProperty()
    {
        return $this->pageService->getPagesWithFilters(
            $this->search,
            $this->filters,
            $this->sortBy,
            $this->sortDirection,
            $this->perPage
        );
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
        $this->authorize('create', Page::class);

        $defaultLocale = config('app.fallback_locale');
        $title = __('messages.page_index.new_page_title');
        $baseSlug = Str::slug($title);
        $slug = $baseSlug;
        $counter = 1;

        // Ensure slug uniqueness
        while (Page::where('slug', $slug)->exists()) {
            $slug = $baseSlug.'-'.$counter;
            $counter++;
        }

        $pageData = [
            'title' => [
                $defaultLocale => $title,
            ],
            'slug' => $slug,
        ];

        $pageDTO = PageDTO::fromArray($pageData);
        $page = $this->pageService->createPage($pageDTO);

        $this->redirectRoute('admin.pages.editor', ['page' => $page->id, 'locale' => $defaultLocale]);
    }

    public function resetFilters(): void
    {
        $this->filters = [];
        $this->showFiltersPopover = false;
    }

    public function confirmDelete(int $id): void
    {
        $page = Page::find($id);
        if ($page) {
            $this->authorize('delete', $page);
            $this->deleteId = $id;
            $this->showDeleteModal = true;
        }
    }

    public function delete(): void
    {
        $page = Page::find($this->deleteId);
        if ($page) {
            $this->authorize('delete', $page);
            $this->pageService->deletePage($page);
        }

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
