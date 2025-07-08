<?php

namespace App\Models;

use App\Traits\HasRevisions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Testimonial extends Model implements HasMedia, Sortable
{
    use HasFactory, HasRevisions, InteractsWithMedia, SortableTrait;

    protected $fillable = [
        'name',
        'title',
        'content',
        'rating',
        'source',
        'order',
    ];

    /**
     * The attributes that should be tracked in revisions.
     */
    protected array $revisionable = [
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

    /**
     * Register media collections for the model.
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('avatar')->singleFile();
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
            'name' => $this->name,
            'title' => $this->title,
            'content' => $this->content,
            'rating' => $this->rating,
            'source' => $this->source,
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

    /**
     * Check if the testimonial has draft changes (unpublished revisions).
     *
     * @return bool True if there are unpublished revisions, false otherwise
     */
    public function hasDraftChanges(): bool
    {
        $latestRevision = $this->latestRevision();
        
        // If no revisions exist, there are no draft changes
        if (!$latestRevision instanceof \App\Models\Revision) {
            return false;
        }
        
        // If the latest revision is not published, there are draft changes
        return !$latestRevision->is_published;
    }
}
