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

    protected $casts = [
        'options' => 'array',
        'subfields' => 'array',
        'callout' => 'array',
    ];

    /**
     * Interact with the setting's value.
     *
     * @return Attribute
     */
    protected function value(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                switch ($this->cast) {
                    case 'array':
                        return is_array($value) ? $value : json_decode($value, true) ?? [];
                    case 'boolean':
                        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
                    case 'integer':
                        return (int) $value;
                    case 'float':
                        return (float) $value;
                    default:
                        return $value;
                }
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
}
