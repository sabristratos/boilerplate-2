<?php

namespace App\Services;

use Illuminate\Support\Facades\File;

class ResourceManager
{
    protected ?array $resources = null;

    public function getResources(): array
    {
        if ($this->resources !== null) {
            return $this->resources;
        }

        $resources = [];
        $path = app_path('Http/Resources/Admin');

        if (! File::isDirectory($path)) {
            return [];
        }

        foreach (File::allFiles($path) as $file) {
            $class = 'App\\Http\\Resources\\Admin\\'.str_replace('.php', '', $file->getFilename());

            if (class_exists($class) && is_subclass_of($class, \App\Services\ResourceSystem\Resource::class)) {
                $resources[] = $class;
            }
        }

        return $this->resources = $resources;
    }

    public function getResourcesWithInstances(): array
    {
        return array_map(fn ($resource): object => new $resource, $this->getResources());
    }
}
