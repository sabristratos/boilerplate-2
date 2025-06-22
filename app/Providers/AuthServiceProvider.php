<?php

namespace App\Providers;

use App\Enums\SettingGroupKey;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        \App\Models\ContentBlock::class => \App\Policies\ContentBlockPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // Register settings permissions
        foreach (SettingGroupKey::cases() as $group) {
            Gate::define("settings.{$group->value}.manage", function ($user) use ($group) {
                return $user->hasPermissionTo("settings.{$group->value}.manage");
            });
        }
    }
}
