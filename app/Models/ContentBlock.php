<?php

namespace App\Models;

use App\Services\BlockManager;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class ContentBlock extends Model implements Sortable, HasMedia
{
    use SortableTrait;
    use InteractsWithMedia;

    protected $fillable = [
        'type',
        'page_id',
        'data',
        'status',
    ];

    protected $casts = [
        'data' => 'array',
        'status' => \App\Enums\ContentBlockStatus::class,
    ];

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

    public function buildSortQuery()
    {
        return static::query()->where('page_id', $this->page_id);
    }

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }
}
