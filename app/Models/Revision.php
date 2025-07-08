<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Revision model for tracking changes to any model.
 *
 * This model stores snapshots of model data and tracks changes
 * for audit trails and version control functionality.
 */
class Revision extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'revisionable_type',
        'revisionable_id',
        'user_id',
        'action',
        'version',
        'data',
        'changes',
        'metadata',
        'description',
        'is_published',
        'published_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'data' => 'array',
            'changes' => 'array',
            'metadata' => 'array',
            'is_published' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    /**
     * Get the user who created this revision.
     *
     * @return BelongsTo<User, Revision>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the model that this revision belongs to.
     */
    public function revisionable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Scope to get only published revisions.
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    /**
     * Scope to get revisions by action.
     */
    public function scopeByAction($query, string $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Scope to get revisions for a specific model.
     */
    public function scopeForModel($query, Model $model)
    {
        return $query->where('revisionable_type', $model::class)
            ->where('revisionable_id', $model->id);
    }

    /**
     * Get the formatted version number.
     */
    protected function formattedVersion(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(get: fn() => $this->version ?? 'v'.$this->id);
    }

    /**
     * Get the human-readable action description.
     */
    protected function actionDescription(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(get: fn() => match ($this->action) {
            'create' => __('revisions.actions.created'),
            'update' => __('revisions.actions.updated'),
            'delete' => __('revisions.actions.deleted'),
            'publish' => __('revisions.actions.published'),
            'revert' => __('revisions.actions.reverted'),
            default => ucfirst($this->action),
        });
    }
}
