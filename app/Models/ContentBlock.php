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
        'status',
        'order',
    ];

    public array $translatable = ['data'];

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

    public function getSettingsArray(): array
    {
        $settings = $this->settings;
        
        if (is_string($settings)) {
            $decoded = json_decode($settings, true);
            return is_array($decoded) ? $decoded : [];
        }
        
        return is_array($settings) ? $settings : [];
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
            'status' => \App\Enums\ContentBlockStatus::class,
            'data' => 'array',
            'settings' => 'array',
        ];
    }
}
