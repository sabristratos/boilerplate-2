<?php

namespace App\Providers;

use App\Enums\SettingGroupKey;
use App\Models\ContentBlock;
use App\Models\Form;
use App\Models\FormSubmission;
use App\Policies\ContentBlockPolicy;
use App\Policies\FormPolicy;
use App\Policies\FormSubmissionPolicy;
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
        ContentBlock::class => ContentBlockPolicy::class,
        Form::class => FormPolicy::class,
        FormSubmission::class => FormSubmissionPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        Gate::before(fn ($user, $ability): ?true => $user->hasRole('Super Admin') ? true : null);

        // Register settings permissions
        foreach (SettingGroupKey::cases() as $group) {
            Gate::define("settings.{$group->value}.manage", fn ($user) => $user->hasPermissionTo("settings.{$group->value}.manage"));
        }
    }
}
