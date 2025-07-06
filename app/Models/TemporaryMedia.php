<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class TemporaryMedia extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'session_id',
        'collection_name',
        'model_type',
        'field_name',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Register media collections for the model.
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('temp')->singleFile();
    }

    /**
     * Get the temporary media for a specific session and field.
     */
    public static function getForSession(string $sessionId, string $fieldName): ?self
    {
        return static::where('session_id', $sessionId)
            ->where('field_name', $fieldName)
            ->first();
    }

    /**
     * Create or update temporary media for a session.
     */
    public static function createForSession(string $sessionId, string $fieldName, string $modelType, string $collectionName): self
    {
        return static::updateOrCreate(
            [
                'session_id' => $sessionId,
                'field_name' => $fieldName,
            ],
            [
                'model_type' => $modelType,
                'collection_name' => $collectionName,
            ]
        );
    }

    /**
     * Clean up old temporary media (older than 24 hours).
     */
    public static function cleanupOld(): int
    {
        return static::where('created_at', '<', now()->subDay())->delete();
    }
} 