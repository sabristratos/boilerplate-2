<?php

declare(strict_types=1);

namespace App\Models;

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
 * @property array $draft_name
 * @property array $settings
 * @property array $draft_settings
 * @property array $elements
 * @property array $draft_elements
 * @property \Carbon\Carbon|null $last_draft_at
 *
 * @method BelongsTo user()
 * @method HasMany submissions()
 */
class Form extends Model
{
    use HasFactory, HasTranslations;

    /**
     * The attributes that are translatable.
     *
     * @var array<string>
     */
    public array $translatable = ['name', 'draft_name'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'user_id',
        'name',
        'draft_name',
        'settings',
        'draft_settings',
        'elements',
        'draft_elements',
        'last_draft_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'settings' => 'array',
        'draft_settings' => 'array',
        'elements' => 'array',
        'draft_elements' => 'array',
        'last_draft_at' => 'datetime',
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
     * Check if this form has draft changes.
     *
     * @return bool True if there are draft changes, false otherwise
     */
    public function hasDraftChanges(): bool
    {
        return ! empty($this->draft_name) ||
               ! empty($this->draft_elements) ||
               ! empty($this->draft_settings);
    }

    /**
     * Get the current elements (draft if available, otherwise published).
     *
     * @return array The current elements array
     */
    public function getCurrentElements(): array
    {
        return ! empty($this->draft_elements) ? $this->draft_elements : ($this->elements ?? []);
    }

    /**
     * Get the current settings (draft if available, otherwise published).
     *
     * @return array The current settings array
     */
    public function getCurrentSettings(): array
    {
        return ! empty($this->draft_settings) ? $this->draft_settings : ($this->settings ?? []);
    }

    /**
     * Get the current name (draft if available, otherwise published).
     *
     * @param  string|null  $locale  The locale to get the name for
     * @return array The current name translations
     */
    public function getCurrentName(?string $locale = null): array
    {
        if ($locale) {
            $draftName = $this->getTranslation('draft_name', $locale);
            $publishedName = $this->getTranslation('name', $locale);

            return ! empty($draftName) ? [$locale => $draftName] : [$locale => $publishedName];
        }

        $draftName = $this->getTranslations('draft_name');
        $publishedName = $this->getTranslations('name');

        return ! empty($draftName) ? $draftName : $publishedName;
    }

    /**
     * Publish the draft changes to the published fields.
     */
    public function publishDraft(): void
    {
        if ($this->hasDraftChanges()) {
            // Copy draft fields to published fields
            if (! empty($this->draft_name)) {
                $this->name = $this->draft_name;
            }

            if (! empty($this->draft_elements)) {
                $this->elements = $this->draft_elements;
            }

            if (! empty($this->draft_settings)) {
                $this->settings = $this->draft_settings;
            }

            // Clear draft fields
            $this->draft_name = null;
            $this->draft_elements = null;
            $this->draft_settings = null;
            $this->last_draft_at = null;

            $this->save();
        }
    }

    /**
     * Discard all draft changes.
     */
    public function discardDraft(): void
    {
        // Clear draft fields
        $this->draft_name = null;
        $this->draft_elements = null;
        $this->draft_settings = null;
        $this->last_draft_at = null;

        $this->save();
    }
}
