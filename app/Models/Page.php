<?php

namespace App\Models;

use App\Enums\PublishStatus;
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
}
