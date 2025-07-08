<?php

namespace App\Models;

use App\Enums\PublishStatus;
use App\Traits\HasRevisions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Translatable\HasTranslations;

class Page extends Model implements HasMedia
{
    use HasFactory;
    use HasRevisions;
    use HasTranslations;
    use InteractsWithMedia;

    protected $fillable = [
        'title',
        'slug',
        'status',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'og_title',
        'og_description',
        'og_image',
        'twitter_title',
        'twitter_description',
        'twitter_image',
        'twitter_card_type',
        'canonical_url',
        'structured_data',
        'no_index',
        'no_follow',
        'no_archive',
        'no_snippet',
    ];

    public array $translatable = [
        'title',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'og_title',
        'og_description',
        'og_image',
        'twitter_title',
        'twitter_description',
        'twitter_image',
        'twitter_card_type',
        'canonical_url',
        'structured_data',
    ];

    public function contentBlocks(): HasMany
    {
        return $this->hasMany(ContentBlock::class);
    }

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function hasTranslation(string $locale): bool
    {
        return array_key_exists($locale, $this->getTranslations('title'));
    }

    protected function casts(): array
    {
        return [
            'status' => PublishStatus::class,
            'no_index' => 'boolean',
            'no_follow' => 'boolean',
            'no_archive' => 'boolean',
            'no_snippet' => 'boolean',
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
            'title' => $this->title,
            'slug' => $this->slug,
            'status' => $this->status,
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,
            'meta_keywords' => $this->meta_keywords,
            'og_title' => $this->og_title,
            'og_description' => $this->og_description,
            'og_image' => $this->og_image,
            'twitter_title' => $this->twitter_title,
            'twitter_description' => $this->twitter_description,
            'twitter_image' => $this->twitter_image,
            'twitter_card_type' => $this->twitter_card_type,
            'canonical_url' => $this->canonical_url,
            'structured_data' => $this->structured_data,
            'no_index' => $this->no_index,
            'no_follow' => $this->no_follow,
            'no_archive' => $this->no_archive,
            'no_snippet' => $this->no_snippet,
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
     * Check if the page has draft changes (unpublished revisions).
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
