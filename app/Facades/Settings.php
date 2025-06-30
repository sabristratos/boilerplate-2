<?php

namespace App\Facades;

use App\Services\SettingsManager;
use Illuminate\Support\Facades\Facade;

/**
 * @method static bool has(string $key)
 * @method static mixed get(string $key, mixed $default = null, ?string $locale = null)
 * @method static mixed getTranslation(string $key, string $locale, mixed $default = null)
 * @method static void set(string $key, mixed $value)
 * @method static void setTranslation(string $key, string $locale, mixed $value)
 * @method static array getAll()
 * @method static void clearCache()
 *
 * @see \App\Services\SettingsManager
 */
class Settings extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return SettingsManager::class;
    }
}
