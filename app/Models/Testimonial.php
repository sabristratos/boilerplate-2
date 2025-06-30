<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Testimonial extends Model implements HasMedia, Sortable
{
    use HasFactory, InteractsWithMedia, SortableTrait;

    protected $fillable = [
        'name',
        'title',
        'content',
        'rating',
        'source',
        'order',
    ];

    public array $sortable = [
        'order_column_name' => 'order',
        'sort_when_creating' => true,
    ];

    /**
     * Get the user's avatar URL.
     */
    protected function avatar(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(get: fn (): string => $this->getFirstMediaUrl('avatar'));
    }

    protected function casts(): array
    {
        return [
            'rating' => 'integer',
            'order' => 'integer',
        ];
    }
}
