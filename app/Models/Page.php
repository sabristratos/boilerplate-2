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
    use HasTranslations;
    use InteractsWithMedia;
    use HasRevisions;

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
        'draft_title',
        'draft_slug',
        'draft_meta_title',
        'draft_meta_description',
        'draft_meta_keywords',
        'draft_og_title',
        'draft_og_description',
        'draft_og_image',
        'draft_twitter_title',
        'draft_twitter_description',
        'draft_twitter_image',
        'draft_twitter_card_type',
        'draft_canonical_url',
        'draft_structured_data',
        'draft_no_index',
        'draft_no_follow',
        'draft_no_archive',
        'draft_no_snippet',
        'last_draft_at',
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
        'draft_title',
        'draft_meta_title',
        'draft_meta_description',
        'draft_meta_keywords',
        'draft_og_title',
        'draft_og_description',
        'draft_og_image',
        'draft_twitter_title',
        'draft_twitter_description',
        'draft_twitter_image',
        'draft_structured_data',
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

    public function hasDraftChanges(): bool
    {
        return ! empty($this->draft_title) ||
               ! empty($this->draft_slug) ||
               ! empty($this->draft_meta_title) ||
               ! empty($this->draft_meta_description) ||
               $this->contentBlocks()->whereNotNull('draft_data')
                   ->orWhereNotNull('draft_settings')
                   ->orWhereNotNull('draft_visible')
                   ->exists();
    }

    public function publishDraft(): void
    {
        if ($this->hasDraftChanges()) {
            // Create a revision before publishing
            $this->createManualRevision('publish', 'Published draft changes');

            // Prevent automatic revision on update
            $this->skipRevision = true;

            // Copy draft fields to published fields
            $this->title = $this->draft_title ?? $this->title;
            $this->slug = $this->draft_slug ?? $this->slug;
            $this->meta_title = $this->draft_meta_title ?? $this->meta_title;
            $this->meta_description = $this->draft_meta_description ?? $this->meta_description;
            $this->meta_keywords = $this->draft_meta_keywords ?? $this->meta_keywords;
            $this->og_title = $this->draft_og_title ?? $this->og_title;
            $this->og_description = $this->draft_og_description ?? $this->og_description;
            $this->og_image = $this->draft_og_image ?? $this->og_image;
            $this->twitter_title = $this->draft_twitter_title ?? $this->twitter_title;
            $this->twitter_description = $this->draft_twitter_description ?? $this->twitter_description;
            $this->twitter_image = $this->draft_twitter_image ?? $this->twitter_image;
            $this->twitter_card_type = $this->draft_twitter_card_type ?? $this->twitter_card_type;
            $this->canonical_url = $this->draft_canonical_url ?? $this->canonical_url;
            $this->structured_data = $this->draft_structured_data ?? $this->structured_data;

            // Handle boolean fields properly
            if ($this->draft_no_index !== null) {
                $this->no_index = $this->draft_no_index;
            }
            if ($this->draft_no_follow !== null) {
                $this->no_follow = $this->draft_no_follow;
            }
            if ($this->draft_no_archive !== null) {
                $this->no_archive = $this->draft_no_archive;
            }
            if ($this->draft_no_snippet !== null) {
                $this->no_snippet = $this->draft_no_snippet;
            }

            // Clear draft fields
            $this->draft_title = null;
            $this->draft_slug = null;
            $this->draft_meta_title = null;
            $this->draft_meta_description = null;
            $this->draft_meta_keywords = null;
            $this->draft_og_title = null;
            $this->draft_og_description = null;
            $this->draft_og_image = null;
            $this->draft_twitter_title = null;
            $this->draft_twitter_description = null;
            $this->draft_twitter_image = null;
            $this->draft_twitter_card_type = null;
            $this->draft_canonical_url = null;
            $this->draft_structured_data = null;
            $this->draft_no_index = null;
            $this->draft_no_follow = null;
            $this->draft_no_archive = null;
            $this->draft_no_snippet = null;
            $this->last_draft_at = null;

            // Publish all content blocks
            foreach ($this->contentBlocks as $block) {
                $block->publishDraft();
            }

            $this->save();

            $this->skipRevision = false;
        }
    }

    public function discardDraft(): void
    {
        // Clear page draft fields
        $this->draft_title = null;
        $this->draft_slug = null;
        $this->draft_meta_title = null;
        $this->draft_meta_description = null;
        $this->draft_meta_keywords = null;
        $this->draft_og_title = null;
        $this->draft_og_description = null;
        $this->draft_og_image = null;
        $this->draft_twitter_title = null;
        $this->draft_twitter_description = null;
        $this->draft_twitter_image = null;
        $this->draft_twitter_card_type = null;
        $this->draft_canonical_url = null;
        $this->draft_structured_data = null;
        $this->draft_no_index = null;
        $this->draft_no_follow = null;
        $this->draft_no_archive = null;
        $this->draft_no_snippet = null;
        $this->last_draft_at = null;

        // Discard all content block drafts
        foreach ($this->contentBlocks as $block) {
            $block->discardDraft();
        }

        $this->save();
    }

    protected function casts(): array
    {
        return [
            'status' => PublishStatus::class,
            'no_index' => 'boolean',
            'no_follow' => 'boolean',
            'no_archive' => 'boolean',
            'no_snippet' => 'boolean',
            'draft_no_index' => 'boolean',
            'draft_no_follow' => 'boolean',
            'draft_no_archive' => 'boolean',
            'draft_no_snippet' => 'boolean',
            'last_draft_at' => 'datetime',
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
            'draft_title' => $this->draft_title,
            'draft_slug' => $this->draft_slug,
            'draft_meta_title' => $this->draft_meta_title,
            'draft_meta_description' => $this->draft_meta_description,
            'draft_meta_keywords' => $this->draft_meta_keywords,
            'draft_og_title' => $this->draft_og_title,
            'draft_og_description' => $this->draft_og_description,
            'draft_og_image' => $this->draft_og_image,
            'draft_twitter_title' => $this->draft_twitter_title,
            'draft_twitter_description' => $this->draft_twitter_description,
            'draft_twitter_image' => $this->draft_twitter_image,
            'draft_twitter_card_type' => $this->draft_twitter_card_type,
            'draft_canonical_url' => $this->draft_canonical_url,
            'draft_structured_data' => $this->draft_structured_data,
            'draft_no_index' => $this->draft_no_index,
            'draft_no_follow' => $this->draft_no_follow,
            'draft_no_archive' => $this->draft_no_archive,
            'draft_no_snippet' => $this->draft_no_snippet,
            'last_draft_at' => $this->last_draft_at,
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
