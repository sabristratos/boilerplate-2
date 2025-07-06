<?php

declare(strict_types=1);

namespace App\Livewire\Admin;

use App\Actions\Content\SaveDraftPageDetailsAction;
use App\Models\Page;
use App\Services\BlockManager;
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
     * Boot the component and inject dependencies.
     */
    public function boot(BlockManager $blockManager): void
    {
        $this->blockManager = $blockManager;
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
        // Load draft data if available, otherwise fall back to published data
        $draftTitle = $this->page->getTranslations('draft_title');
        $this->title = ! empty($draftTitle) ? $draftTitle : $this->page->getTranslations('title');

        $this->slug = $this->page->draft_slug ?? $this->page->slug;

        $draftMetaTitle = $this->page->getTranslations('draft_meta_title');
        $this->meta_title = ! empty($draftMetaTitle) ? $draftMetaTitle : $this->page->getTranslations('meta_title');

        $draftMetaDescription = $this->page->getTranslations('draft_meta_description');
        $this->meta_description = ! empty($draftMetaDescription) ? $draftMetaDescription : $this->page->getTranslations('meta_description');

        $this->no_index = $this->page->draft_no_index !== null ? $this->page->draft_no_index : $this->page->no_index;
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
     * Save the page details.
     */
    public function savePage(): void
    {
        try {
            $saveDraftPageDetailsAction = app(SaveDraftPageDetailsAction::class);
            $saveDraftPageDetailsAction->execute($this->page, [
                'title' => $this->title,
                'slug' => $this->slug,
                'meta_title' => $this->meta_title,
                'meta_description' => $this->meta_description,
                'no_index' => $this->no_index,
            ]);

            $this->showSuccessToast(
                __('messages.page_manager.page_saved_text'),
                __('messages.page_manager.page_saved_title')
            );

        } catch (\Exception $e) {
            $this->showErrorToast(
                __('messages.page_manager.page_save_failed_text'),
                __('messages.page_manager.page_save_failed_title')
            );
        }
    }

    /**
     * Save the page as draft.
     */
    public function saveDraft(): void
    {
        try {
            $saveDraftPageDetailsAction = app(SaveDraftPageDetailsAction::class);
            $saveDraftPageDetailsAction->execute($this->page, [
                'title' => $this->title,
                'slug' => $this->slug,
                'meta_title' => $this->meta_title,
                'meta_description' => $this->meta_description,
                'no_index' => $this->no_index,
            ]);

            $this->showSuccessToast(
                __('messages.page_manager.draft_saved_text'),
                __('messages.page_manager.draft_saved_title')
            );

        } catch (\Exception $e) {
            $this->showErrorToast(
                __('messages.page_manager.draft_save_failed_text'),
                __('messages.page_manager.draft_save_failed_title')
            );
        }
    }

    /**
     * Publish the page draft.
     */
    public function publishPage(): void
    {
        try {
            // First save any current changes as draft
            $saveDraftPageDetailsAction = app(SaveDraftPageDetailsAction::class);
            $saveDraftPageDetailsAction->execute($this->page, [
                'title' => $this->title,
                'slug' => $this->slug,
                'meta_title' => $this->meta_title,
                'meta_description' => $this->meta_description,
                'no_index' => $this->no_index,
            ]);

            // Then publish the draft
            $this->page->publishDraft();

            $this->showSuccessToast(
                __('messages.page_manager.page_published_text'),
                __('messages.page_manager.page_published_title')
            );

        } catch (\Exception $e) {
            $this->showErrorToast(
                __('messages.page_manager.page_publish_failed_text'),
                __('messages.page_manager.page_publish_failed_title')
            );
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
