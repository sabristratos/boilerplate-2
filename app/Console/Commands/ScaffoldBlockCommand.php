<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class ScaffoldBlockCommand extends Command
{
    protected $signature = 'make:block {name}';
    protected $description = 'commands.make_block.description';

    protected Filesystem $files;

    public function __construct(Filesystem $files)
    {
        parent::__construct();
        $this->files = $files;
    }

    public function handle()
    {
        $name = $this->argument('name');

        $this->createBlockClass($name);
        $this->createAdminView($name);
        $this->createFrontendView($name);

        $this->info("Block [{$name}] created successfully.");
        $this->info("Class: app/Blocks/{$name}Block.php");
        $this->info("Admin View: resources/views/livewire/admin/block-forms/_{$this->getKebabName($name)}.blade.php");
        $this->info("Frontend View: resources/views/frontend/blocks/_{$this->getKebabName($name)}.blade.php");
    }

    protected function createBlockClass($name)
    {
        $path = app_path("Blocks/{$name}Block.php");
        $this->createDirectory(dirname($path));

        $stub = $this->getStubContent('block.class.stub');
        $content = $this->replacePlaceholders($stub, $name);

        $this->files->put($path, $content);
    }

    protected function createAdminView($name)
    {
        $kebabName = $this->getKebabName($name);
        $path = resource_path("views/livewire/admin/block-forms/_{$kebabName}.blade.php");
        $this->createDirectory(dirname($path));

        $stub = $this->getStubContent('block.admin-view.stub');
        $content = $this->replacePlaceholders($stub, $name);

        $this->files->put($path, $content);
    }

    protected function createFrontendView($name)
    {
        $kebabName = $this->getKebabName($name);
        $path = resource_path("views/frontend/blocks/_{$kebabName}.blade.php");
        $this->createDirectory(dirname($path));

        $stub = $this->getStubContent('block.frontend-view.stub');
        $content = $this->replacePlaceholders($stub, $name);

        $this->files->put($path, $content);
    }

    protected function getStubContent($stubName)
    {
        return $this->files->get(base_path("stubs/{$stubName}"));
    }

    protected function replacePlaceholders($stub, $name)
    {
        $kebabName = $this->getKebabName($name);
        $titleName = $this->getTitleName($name);

        return str_replace(
            ['{{ className }}', '{{ kebabName }}', '{{ titleName }}'],
            [$name, $kebabName, $titleName],
            $stub
        );
    }

    protected function createDirectory($path)
    {
        if (! $this->files->isDirectory($path)) {
            $this->files->makeDirectory($path, 0755, true, true);
        }
    }

    protected function getKebabName($name)
    {
        return Str::kebab(Str::beforeLast($name, 'Block'));
    }

    protected function getTitleName($name)
    {
        return Str::title(Str::snake(Str::beforeLast($name, 'Block'), ' '));
    }
} 