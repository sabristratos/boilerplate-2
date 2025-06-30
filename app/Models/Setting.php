<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Translatable\HasTranslations;

class Setting extends Model implements HasMedia
{
    use HasFactory;
    use HasTranslations;
    use InteractsWithMedia;

    /**
     * The attributes that are translatable.
     *
     * @var array<int, string>
     */
    public array $translatable = ['label', 'description', 'callout'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [];

    /**
     * Interact with the setting's value.
     */
    protected function value(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => match ($this->cast) {
                'array' => is_array($value) ? $value : json_decode((string) $value, true) ?? [],
                'boolean' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
                'integer' => (int) $value,
                'float' => (float) $value,
                default => $value,
            },
            set: fn ($value) => match ($this->cast) {
                'array' => json_encode($value),
                default => $value,
            }
        );
    }

    /**
     * Get the setting group that owns the setting.
     */
    public function settingGroup(): BelongsTo
    {
        return $this->belongsTo(SettingGroup::class);
    }

    /**
     * Register media collections for the model.
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('default')
            ->singleFile();
    }

    protected function casts(): array
    {
        return [
            'options' => 'array',
            'subfields' => 'array',
            'callout' => 'array',
        ];
    }
}
