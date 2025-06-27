<?php

namespace App\Models;

use App\Services\BlockManager;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Translatable\HasTranslations;

class ContentBlock extends Model implements Sortable, HasMedia
{
    use SortableTrait;
    use InteractsWithMedia;
    use HasTranslations;

    protected static function booted()
    {
        static::creating(function ($block) {
            Log::info('Creating content block', [
                'user_id' => auth()->id(),
                'type' => $block->type,
                'page_id' => $block->page_id,
            ]);
        });

        static::updating(function ($block) {
            Log::info('Updating content block', [
                'user_id' => auth()->id(),
                'block_id' => $block->id,
                'changes' => $block->getDirty(),
            ]);
        });

        static::deleting(function ($block) {
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

    protected $casts = [
        'status' => \App\Enums\ContentBlockStatus::class,
        'data' => 'array',
        'settings' => 'array',
    ];

    public array $translatable = ['data'];

    public array $sortable = [
        'order_column_name' => 'order',
        'sort_when_creating' => true,
    ];

    public function getDataAttribute($value)
    {
        // When called via HasTranslations, $value is already a decoded array for the current locale.
        // Otherwise, it might be a JSON string from the database.
        $dataForLocale = is_string($value) ? json_decode($value, true) : $value;
        $dataForLocale = $dataForLocale ?? [];

        $blockClass = app(BlockManager::class)->find($this->type);
        $defaultData = $blockClass ? $blockClass->getDefaultData() : [];

        return array_merge($defaultData, $dataForLocale);
    }

    public function blockClass(): Attribute
    {
        return Attribute::make(
            get: fn () => app(BlockManager::class)->find($this->type),
        );
    }

    public function buildSortQuery()
    {
        return static::query()->where('page_id', $this->page_id);
    }

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }
}
