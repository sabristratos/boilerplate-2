<?php

namespace App\Providers;

use App\Services\BlockManager;
use App\Services\ResourceManager;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ResourceManagerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(BlockManager::class, fn ($app): \App\Services\BlockManager => new BlockManager);

        $this->app->singleton(ResourceManager::class, fn ($app): \App\Services\ResourceManager => new ResourceManager);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->registerRoutes();
        $this->registerViewComposers();
        $this->registerBladeComponents();
    }

    /**
     * Register the routes.
     *
     * @return void
     */
    protected function registerRoutes()
    {
        Route::middleware(['web', 'auth'])
            ->prefix('admin')
            ->name('admin.')
            ->group(function (): void {
                try {
                    foreach ($this->app->make(ResourceManager::class)->getResources() as $resource) {
                        $uriKey = $resource::uriKey();
                        $permission = $resource::$permission;

                        $routeGroup = Route::name("resources.{$uriKey}.");

                        if ($permission) {
                            $routeGroup->middleware("can:{$permission}");
                        }

                        $routeGroup->group(function () use ($resource, $uriKey): void {
                            Route::get($uriKey, fn () => view('resource-system.index', [
                                'resource' => new $resource,
                            ]))->name('index');

                            Route::get("{$uriKey}/create", fn () => view('resource-system.create', [
                                'resource' => new $resource,
                            ]))->name('create');

                            Route::get("{$uriKey}/{id}/edit", fn ($id) => view('resource-system.edit', [
                                'resource' => new $resource,
                                'resourceId' => $id,
                            ]))->name('edit');
                        });
                    }
                } catch (\Exception) {
                    // If there's an error (like missing database tables), just skip registering these routes
                }
            });
    }

    /**
     * Register the view composers.
     *
     * @return void
     */
    protected function registerViewComposers()
    {
        View::composer('components.layouts.app.sidebar', function ($view): void {
            try {
                $view->with('resources', $this->app->make(ResourceManager::class)->getResourcesWithInstances());
            } catch (\Exception) {
                // If there's an error (like missing database tables), just provide an empty array
                $view->with('resources', []);
            }
        });
    }

    /**
     * Register the Blade components.
     *
     * @return void
     */
    protected function registerBladeComponents()
    {
        $this->loadViewsFrom(__DIR__.'/../../resources/views/resource-system', 'resource-system');
    }
}
