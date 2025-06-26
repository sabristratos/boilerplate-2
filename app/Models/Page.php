<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Page extends Model implements HasMedia
{
    use HasFactory;
    use HasTranslations;
    use InteractsWithMedia;

    protected $fillable = [
        'title',
        'slug',
    ];

    public array $translatable = ['title', 'slug'];

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

    /**
     * Retrieve the model for a bound value.
     *
     * @param  mixed  $value
     * @param  string|null  $field
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function resolveRouteBinding($value, $field = null)
    {
        // Add debugging
        \Illuminate\Support\Facades\Log::info('Resolving route binding for Page', [
            'value' => $value,
            'field' => $field,
            'locale' => app()->getLocale(),
            'is_numeric' => is_numeric($value),
            'route_name' => request()->route()->getName(),
        ]);

        // If the value is numeric and we're on the admin.pages.editor route, find by ID
        if (is_numeric($value) && request()->route()->getName() === 'admin.pages.editor') {
            return $this->where('id', $value)->firstOrFail();
        }

        // Otherwise, find by slug in the current locale
        return $this->where("slug->" . app()->getLocale(), $value)->firstOrFail();
    }

    public function hasTranslation(string $locale): bool
    {
        return array_key_exists($locale, $this->getTranslations('title'));
    }
}
