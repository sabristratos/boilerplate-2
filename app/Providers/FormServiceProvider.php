<?php

namespace App\Providers;

use App\Forms\FieldTypeManager;
use Illuminate\Support\ServiceProvider;

class FormServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(FieldTypeManager::class, function () {
            $manager = new FieldTypeManager();
            
            $fieldTypes = config('forms.field_types', []);
            foreach ($fieldTypes as $class) {
                $manager->register($class);
            }
            
            return $manager;
        });
    }

    public function boot(): void
    {
        //
    }
} 