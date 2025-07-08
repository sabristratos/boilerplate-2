<?php

declare(strict_types=1);

namespace App\Models;

use App\Services\BlockManager;
use App\Traits\HasRevisions;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
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
    use HasFactory;
    use HasRevisions;
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
            // Only log significant changes, not every auto-save
            $changes = $block->getDirty();
            $significantChanges = array_diff_key($changes, array_flip([]));

            if ($significantChanges !== []) {
                Log::info('Updating content block', [
                    'user_id' => auth()->id(),
                    'block_id' => $block->id,
                    'changes' => $significantChanges,
                ]);
            }
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
        'visible',
        'order',
    ];

    /**
     * The attributes that should be translatable.
     *
     * @var array<string>
     */
    public array $translatable = ['data'];

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
     * Check if this block is currently visible.
     *
     * @return bool True if the block is visible, false otherwise
     */
    public function isVisible(): bool
    {
        return (bool) $this->getAttribute('visible');
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
            'visible' => 'boolean',
            'order' => 'integer',
        ];
    }

    /**
     * Get the revision data that should be tracked.
     *
     * @return array<string, mixed>
     */
    public function getRevisionData(): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'page_id' => $this->page_id,
            'data' => $this->data,
            'settings' => $this->settings,
            'visible' => $this->visible,
            'order' => $this->order,
        ];
    }

    /**
     * Get the fields that should be excluded from revision tracking.
     *
     * @return array<string>
     */
    public function getRevisionExcludedFields(): array
    {
        return [
            'created_at',
            'updated_at',
            'deleted_at',
        ];
    }
}
