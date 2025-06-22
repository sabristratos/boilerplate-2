<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;
use App\Services\BlockManager;
use App\Services\ResourceManager;

class ResourceManagerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(BlockManager::class, function ($app) {
            return new BlockManager();
        });

        $this->app->singleton(ResourceManager::class, function ($app) {
            return new ResourceManager();
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
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
            ->group(function () {
                try {
                    foreach ($this->app->make(ResourceManager::class)->getResources() as $resource) {
                        $uriKey = $resource::uriKey();
                        $permission = $resource::$permission;

                        $routeGroup = Route::name("resources.{$uriKey}.");

                        if ($permission) {
                            $routeGroup->middleware("can:{$permission}");
                        }

                        $routeGroup->group(function () use ($resource, $uriKey) {
                            Route::get($uriKey, function () use ($resource) {
                                return view('resource-system.index', [
                                    'resource' => new $resource,
                                ]);
                            })->name("index");

                            Route::get("{$uriKey}/create", function () use ($resource) {
                                return view('resource-system.create', [
                                    'resource' => new $resource,
                                ]);
                            })->name("create");

                            Route::get("{$uriKey}/{id}/edit", function ($id) use ($resource) {
                                return view('resource-system.edit', [
                                    'resource' => new $resource,
                                    'resourceId' => $id,
                                ]);
                            })->name("edit");
                        });
                    }
                } catch (\Exception $e) {
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
        View::composer('components.layouts.app.sidebar', function ($view) {
            try {
                $view->with('resources', $this->app->make(ResourceManager::class)->getResourcesWithInstances());
            } catch (\Exception $e) {
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
