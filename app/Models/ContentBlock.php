<?php

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

class ContentBlock extends Model implements HasMedia, Sortable
{
    use HasTranslations;
    use InteractsWithMedia;
    use SortableTrait;

    protected static function booted()
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

    public array $translatable = ['data', 'draft_data'];

    public array $sortable = [
        'order_column_name' => 'order',
        'sort_when_creating' => true,
    ];

    public function blockClass(): Attribute
    {
        return Attribute::make(
            get: fn () => app(BlockManager::class)->find($this->type),
        );
    }

    public function getTranslatedData(string $locale = null): array
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

    public function getDraftTranslatedData(string $locale = null): array
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

    public function getSettingsArray(): array
    {
        $settings = $this->settings;
        
        if (is_string($settings)) {
            $decoded = json_decode($settings, true);
            return is_array($decoded) ? $decoded : [];
        }
        
        return is_array($settings) ? $settings : [];
    }

    public function getDraftSettingsArray(): array
    {
        $settings = $this->draft_settings;
        
        if (is_string($settings)) {
            $decoded = json_decode($settings, true);
            return is_array($decoded) ? $decoded : [];
        }
        
        return is_array($settings) ? $settings : [];
    }

    public function hasDraftChanges(): bool
    {
        $visibilityChanged = $this->draft_visible !== null && $this->draft_visible !== $this->visible;
        return !empty($this->draft_data) || !empty($this->draft_settings) || $visibilityChanged;
    }

    public function isVisible(): bool
    {
        $value = $this->draft_visible !== null
            ? $this->getAttribute('draft_visible')
            : $this->getAttribute('visible');
        return (bool) $value;
    }

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

    public function discardDraft(): void
    {
        $this->draft_data = null;
        $this->draft_settings = null;
        $this->draft_visible = null;
        $this->last_draft_at = null;
        $this->save();
    }

    public function buildSortQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return static::query()->where('page_id', $this->page_id);
    }

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }

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
