<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\HasRevisions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

/**
 * Model representing a user-defined form.
 *
 * @property int $id
 * @property int $user_id
 * @property array $name
 * @property array $elements
 * @property array $settings
 *
 * @method BelongsTo user()
 * @method HasMany submissions()
 */
class Form extends Model
{
    use HasFactory, HasRevisions, HasTranslations;

    /**
     * The attributes that are translatable.
     *
     * @var array<string>
     */
    public array $translatable = ['name'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'user_id',
        'name',
        'elements',
        'settings',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'settings' => 'array',
        'elements' => 'array',
    ];

    /**
     * Get the user that owns the form.
     *
     * @return BelongsTo<User, Form>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the submissions for the form.
     *
     * @return HasMany<FormSubmission>
     */
    public function submissions(): HasMany
    {
        return $this->hasMany(FormSubmission::class);
    }

    /**
     * Get the current elements (published).
     *
     * @return array The current elements array
     */
    public function getCurrentElements(): array
    {
        return $this->elements ?? [];
    }

    /**
     * Get the current settings (published).
     *
     * @return array The current settings array
     */
    public function getCurrentSettings(): array
    {
        return $this->settings ?? [];
    }

    /**
     * Get the current name (published), filtering out empty translations.
     *
     * @param  string|null  $locale  The locale to get the name for
     * @return array The current name translations
     */
    public function getCurrentName(?string $locale = null): array
    {
        if ($locale) {
            return $this->getTranslation('name', $locale, false);
        }

        return array_filter($this->getTranslations('name'), fn ($v) => ! empty($v) && $v !== '[]');
    }

    /**
     * Get the revision data that should be tracked.
     *
     * @return array<string, mixed>
     */
    public function getRevisionData(): array
    {
        // Always return name as associative array with locale keys
        $name = $this->getTranslations('name');

        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'name' => $name,
            'elements' => $this->elements,
            'settings' => $this->settings,
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
     * Check if the form has draft changes (unpublished revisions).
     *
     * @return bool True if there are unpublished revisions, false otherwise
     */
    public function hasDraftChanges(): bool
    {
        $latestRevision = $this->latestRevision();
        
        // If no revisions exist, there are no draft changes
        if (!$latestRevision) {
            return false;
        }
        
        // If the latest revision is not published, there are draft changes
        return !$latestRevision->is_published;
    }
}
