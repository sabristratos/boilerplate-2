<?php

declare(strict_types=1);

namespace App\Livewire\Admin;

use App\DTOs\PageDTO;
use App\Models\Page;
use App\Services\BlockManager;
use App\Services\PageService;
use App\Traits\WithToastNotifications;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

/**
 * Page Manager Livewire component for editing pages and their content blocks.
 *
 * This component provides the main interface for managing page content,
 * delegating specific functionality to child components.
 */
#[Layout('components.layouts.editors')]
class PageManager extends Component
{
    use WithToastNotifications;

    /**
     * The page being edited.
     */
    public Page $page;

    // Page-level properties (draft versions)
    /**
     * Page title translations.
     *
     * @var array<string, string>
     */
    public array $title = [];

    /**
     * Page slug.
     */
    public ?string $slug = '';

    /**
     * Page meta title translations.
     *
     * @var array<string, string>
     */
    public array $meta_title = [];

    /**
     * Page meta description translations.
     *
     * @var array<string, string>
     */
    public array $meta_description = [];

    /**
     * Whether the page should be indexed by search engines.
     */
    public bool $no_index = false;

    // Locale management
    /**
     * Currently active locale for editing.
     */
    public string $activeLocale;

    /**
     * Available locales for the application.
     *
     * @var array<string, string>
     */
    public array $availableLocales = [];

    /**
     * Locale to switch to.
     */
    public ?string $switchLocale = null;

    // UI state
    /**
     * Currently active tab in the editor.
     */
    public string $tab = 'settings';

    /**
     * Block manager service instance.
     */
    protected BlockManager $blockManager;

    /**
     * Page service instance.
     */
    protected PageService $pageService;

    /**
     * Boot the component and inject dependencies.
     */
    public function boot(BlockManager $blockManager, PageService $pageService): void
    {
        $this->blockManager = $blockManager;
        $this->pageService = $pageService;
    }

    /**
     * Mount the component with the page to edit.
     */
    public function mount(Page $page): void
    {
        $this->authorize('update', $page);

        $this->page = $page;
        $this->initializeLocale();
        $this->loadPageTranslations();
    }

    /**
     * Initialize locale settings for the component.
     */
    protected function initializeLocale(): void
    {
        $this->availableLocales = $this->getAvailableLocales();
        $requestedLocale = request()->query('locale', config('app.fallback_locale'));

        // Validate locale format (2-3 character language code)
        if (! preg_match('/^[a-z]{2,3}$/', $requestedLocale)) {
            $requestedLocale = config('app.fallback_locale');
        }

        $this->activeLocale = array_key_exists($requestedLocale, $this->availableLocales)
            ? $requestedLocale
            : config('app.fallback_locale');

        $this->switchLocale = $this->activeLocale;
        app()->setLocale($this->activeLocale);
    }

    /**
     * Load page translations into the component state.
     */
    protected function loadPageTranslations(): void
    {
        $latestRevision = $this->page->latestRevision();

        if ($latestRevision) {
            $data = $latestRevision->data;
            $this->title = is_array($data['title'] ?? null)
                ? $data['title']
                : [$this->activeLocale => ($data['title'] ?? '')];
            $this->slug = $data['slug'] ?? '';
            $this->meta_title = $data['meta_title'] ?? [];
            $this->meta_description = $data['meta_description'] ?? [];
            $this->no_index = $data['no_index'] ?? false;
        } else {
            $this->title = $this->page->getTranslations('title');
            $this->slug = $this->page->slug;
            $this->meta_title = $this->page->getTranslations('meta_title');
            $this->meta_description = $this->page->getTranslations('meta_description');
            $this->no_index = $this->page->no_index;
        }
    }

    /**
     * Get available locales from settings.
     *
     * @return array<string, string>
     */
    protected function getAvailableLocales(): array
    {
        $localesSetting = app('settings')->get('general.available_locales', []);

        return collect($localesSetting)->pluck('name', 'code')->all();
    }

    /**
     * Handle locale switching.
     */
    public function updatedSwitchLocale(string $locale): void
    {
        if (array_key_exists($locale, $this->availableLocales)) {
            $this->redirect(route('admin.pages.editor', ['page' => $this->page, 'locale' => $locale]));
        }
    }

    /**
     * Get the blocks for the current page.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getBlocksProperty()
    {
        return $this->page->contentBlocks()->ordered()->get();
    }

    /**
     * Generate a slug from the page title.
     */
    public function generateSlug(): void
    {
        $title = $this->title[$this->activeLocale] ?? '';
        $this->slug = Str::slug($title);
    }

    /**
     * Save the page as a draft.
     */
    public function savePage(): void
    {
        try {
            $pageData = [
                'id' => $this->page->id,
                'title' => $this->title,
                'slug' => $this->slug,
                'meta_title' => $this->meta_title,
                'meta_description' => $this->meta_description,
                'no_index' => $this->no_index,
            ];

            $pageDTO = PageDTO::fromArray($pageData);
            $this->pageService->updatePage($this->page, $pageDTO, 'draft', 'Saved page draft');

            $this->showSuccessToast('Draft saved successfully.');
        } catch (\Exception $e) {
            $this->showErrorToast('Failed to save draft.');
        }
    }

    /**
     * Publish the page.
     */
    public function publishPage(): void
    {
        try {
            $pageData = [
                'id' => $this->page->id,
                'title' => $this->title,
                'slug' => $this->slug,
                'meta_title' => $this->meta_title,
                'meta_description' => $this->meta_description,
                'no_index' => $this->no_index,
            ];

            $pageDTO = PageDTO::fromArray($pageData);
            $this->pageService->updatePage($this->page, $pageDTO, 'publish', 'Published page', true);

            $this->showSuccessToast('Page published successfully.');
        } catch (\Exception $e) {
            $this->showErrorToast('Failed to publish page.');
        }
    }

    /**
     * Handle block creation events from child components.
     */
    #[On('block-created')]
    public function handleBlockCreated(array $data): void
    {
        $this->showSuccessToast(
            __('messages.page_manager.block_created_text'),
            __('messages.page_manager.block_created_title')
        );
    }

    /**
     * Handle block deletion events from child components.
     */
    #[On('block-deleted')]
    public function handleBlockDeleted(array $data): void
    {
        $this->showSuccessToast(
            __('messages.page_manager.block_deleted_text'),
            __('messages.page_manager.block_deleted_title')
        );
    }

    /**
     * Handle block order update events from child components.
     */
    #[On('block-order-updated')]
    public function handleBlockOrderUpdated(array $data): void
    {
        $this->showSuccessToast(
            __('messages.page_manager.block_order_updated_text'),
            __('messages.page_manager.block_order_updated_title')
        );
    }

    /**
     * Render the component.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('livewire.admin.page-manager');
    }
}
