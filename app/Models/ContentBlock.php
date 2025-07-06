<?php

declare(strict_types=1);

namespace App\Models;

use App\Services\BlockManager;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Log;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Translatable\HasTranslations;

/**
 * Content Block model for the page builder system.
 *
 * This model represents a content block within a page. Each block
 * has a type, data, settings, and can be ordered. It supports
 * translatable content, media attachments, and draft/published states.
 */
class ContentBlock extends Model implements HasMedia, Sortable
{
    use HasTranslations;
    use InteractsWithMedia;
    use SortableTrait;

    /**
     * Boot the model and register event listeners.
     */
    protected static function booted(): void
    {
        static::creating(function ($block): void {
            Log::info('Creating content block', [
                'user_id' => auth()->id(),
                'type' => $block->type,
                'page_id' => $block->page_id,
            ]);
        });

        static::updating(function ($block): void {
            Log::info('Updating content block', [
                'user_id' => auth()->id(),
                'block_id' => $block->id,
                'changes' => $block->getDirty(),
            ]);
        });

        static::deleting(function ($block): void {
            Log::info('Deleting content block', [
                'user_id' => auth()->id(),
                'block_id' => $block->id,
            ]);
        });
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'type',
        'page_id',
        'data',
        'settings',
        'draft_data',
        'draft_settings',
        'visible',
        'draft_visible',
        'order',
        'last_draft_at',
    ];

    /**
     * The attributes that should be translatable.
     *
     * @var array<string>
     */
    public array $translatable = ['data', 'draft_data'];

    /**
     * The sortable configuration.
     *
     * @var array<string, mixed>
     */
    public array $sortable = [
        'order_column_name' => 'order',
        'sort_when_creating' => true,
    ];

    /**
     * Get the block class instance for this block.
     *
     * @return Attribute<Block|null>
     */
    public function blockClass(): Attribute
    {
        return Attribute::make(
            get: fn () => app(BlockManager::class)->find($this->type),
        );
    }

    /**
     * Get the translated data for a specific locale.
     *
     * @param  string|null  $locale  The locale to get data for (defaults to current locale)
     * @return array<string, mixed> The translated data array
     */
    public function getTranslatedData(?string $locale = null): array
    {
        // Ensure we have a valid locale
        if ($locale === null) {
            $locale = app()->getLocale() ?: config('app.fallback_locale', 'en');
        }

        $data = $this->getTranslation('data', $locale);

        if (is_string($data)) {
            $decoded = json_decode($data, true);

            return is_array($decoded) ? $decoded : [];
        }

        return is_array($data) ? $data : [];
    }

    /**
     * Get the draft translated data for a specific locale.
     *
     * @param  string|null  $locale  The locale to get draft data for (defaults to current locale)
     * @return array<string, mixed> The draft translated data array
     */
    public function getDraftTranslatedData(?string $locale = null): array
    {
        // Ensure we have a valid locale
        if ($locale === null) {
            $locale = app()->getLocale() ?: config('app.fallback_locale', 'en');
        }

        $data = $this->getTranslation('draft_data', $locale);

        if (is_string($data)) {
            $decoded = json_decode($data, true);

            return is_array($decoded) ? $decoded : [];
        }

        return is_array($data) ? $data : [];
    }

    /**
     * Get the settings as an array.
     *
     * @return array<string, mixed> The settings array
     */
    public function getSettingsArray(): array
    {
        $settings = $this->settings;

        if (is_string($settings)) {
            $decoded = json_decode($settings, true);

            return is_array($decoded) ? $decoded : [];
        }

        return is_array($settings) ? $settings : [];
    }

    /**
     * Get the draft settings as an array.
     *
     * @return array<string, mixed> The draft settings array
     */
    public function getDraftSettingsArray(): array
    {
        $settings = $this->draft_settings;

        if (is_string($settings)) {
            $decoded = json_decode($settings, true);

            return is_array($decoded) ? $decoded : [];
        }

        return is_array($settings) ? $settings : [];
    }

    /**
     * Check if this block has draft changes.
     *
     * @return bool True if there are draft changes, false otherwise
     */
    public function hasDraftChanges(): bool
    {
        $visibilityChanged = $this->draft_visible !== null && $this->draft_visible !== $this->visible;

        return ! empty($this->draft_data) || ! empty($this->draft_settings) || $visibilityChanged;
    }

    /**
     * Check if this block is currently visible.
     *
     * Uses draft visibility if available, otherwise falls back to published visibility.
     *
     * @return bool True if the block is visible, false otherwise
     */
    public function isVisible(): bool
    {
        $value = $this->draft_visible !== null
            ? $this->getAttribute('draft_visible')
            : $this->getAttribute('visible');

        return (bool) $value;
    }

    /**
     * Publish the draft changes to the published state.
     *
     * This method copies draft data and settings to the published fields
     * and clears the draft data.
     */
    public function publishDraft(): void
    {
        $hasChanges = $this->hasDraftChanges();

        // Only handle visibility changes if draft_visible is explicitly set
        if ($this->draft_visible !== null) {
            $this->visible = (bool) $this->draft_visible;
            $hasChanges = true;
        }

        if ($hasChanges) {
            // Copy draft data to published data
            $this->data = $this->draft_data;
            $this->settings = $this->draft_settings;

            // Clear draft data
            $this->draft_data = null;
            $this->draft_settings = null;
            $this->draft_visible = null;
            $this->last_draft_at = null;

            $this->save();
        }
    }

    /**
     * Discard all draft changes.
     *
     * This method clears all draft data and settings, reverting to
     * the published state.
     */
    public function discardDraft(): void
    {
        $this->draft_data = null;
        $this->draft_settings = null;
        $this->draft_visible = null;
        $this->last_draft_at = null;
        $this->save();
    }

    /**
     * Build the sort query for this block.
     */
    public function buildSortQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return static::query()->where('page_id', $this->page_id);
    }

    /**
     * Get the page that this block belongs to.
     *
     * @return BelongsTo<Page, ContentBlock>
     */
    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }

    /**
     * Get the attribute casts for the model.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'data' => 'array',
            'settings' => 'array',
            'draft_data' => 'array',
            'draft_settings' => 'array',
            'visible' => 'boolean',
            'draft_visible' => 'boolean',
            'last_draft_at' => 'datetime',
        ];
    }
}
