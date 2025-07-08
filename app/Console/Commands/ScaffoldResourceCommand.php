<?php

namespace App\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

#[AsCommand(name: 'make:resource')]
class ScaffoldResourceCommand extends GeneratorCommand
{
    protected $name = 'make:resource';

    protected $description = 'Create a new resource class';

    protected $type = 'Resource';

    protected function getStub()
    {
        return $this->resolveStubPath('/stubs/resource.stub');
    }

    protected function resolveStubPath(string $stub)
    {
        return file_exists($customPath = $this->laravel->basePath(trim($stub, '/')))
            ? $customPath
            : __DIR__.$stub;
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\Http\Resources\Admin';
    }

    protected function buildClass($name)
    {
        $stub = parent::buildClass($name);

        $model = $this->argument('model');
        $modelClass = 'App\\Models\\'.$model;

        if (! class_exists($modelClass)) {
            $this->error("Model {$modelClass} does not exist.");

            return false;
        }

        $stub = str_replace('{{ modelClass }}', $modelClass, $stub);
        $stub = str_replace('{{ model }}', $model, $stub);
        $stub = str_replace('{{ modelLower }}', Str::lower($model), $stub);
        $stub = str_replace('{{ modelPluralLower }}', Str::plural(Str::lower($model)), $stub);

        $fields = $this->generateFields($modelClass);
        $columns = $this->generateColumns($modelClass);
        $imports = array_merge($fields['imports'], $columns['imports']);

        $stub = str_replace('{{ imports }}', implode("\n", array_unique($imports)), $stub);
        $stub = str_replace('{{ fields }}', implode(",\n            ", $fields['code']), $stub);

        return str_replace('{{ columns }}', implode(",\n            ", $columns['code']), $stub);
    }

    protected function generateFields($modelClass): array
    {
        $model = new $modelClass;
        $table = $model->getTable();
        $columns = Schema::getColumnListing($table);
        $imports = [];
        $fields = [];

        foreach ($columns as $column) {
            if (in_array($column, ['id', 'created_at', 'updated_at', 'deleted_at'])) {
                continue;
            }

            if (Str::endsWith($column, '_id')) {
                $imports[] = 'use App\Services\ResourceSystem\Fields\BelongsTo;';
                $relationName = Str::beforeLast($column, '_id');
                $fields[] = "BelongsTo::make('".Str::title($relationName)."')";

                continue;
            }

            $type = Schema::getColumnType($table, $column);

            switch ($type) {
                case 'string':
                default:
                    $imports[] = 'use App\Services\ResourceSystem\Fields\Text;';
                    $fields[] = "Text::make('{$column}')";
                    break;
                case 'text':
                    $imports[] = 'use App\Services\ResourceSystem\Fields\Textarea;';
                    $fields[] = "Textarea::make('{$column}')";
                    break;
                case 'boolean':
                    $imports[] = 'use App\Services\ResourceSystem\Fields\Select;';
                    $fields[] = "Select::make('{$column}')->options([\n                1 => 'Yes',\n                0 => 'No',\n            ])";
                    break;
                case 'date':
                case 'datetime':
                case 'timestamp':
                    $imports[] = 'use App\Services\ResourceSystem\Fields\DatePicker;';
                    $fields[] = "DatePicker::make('{$column}')";
                    break;
            }
        }

        return ['imports' => $imports, 'code' => $fields];
    }

    protected function generateColumns($modelClass): array
    {
        $model = new $modelClass;
        $table = $model->getTable();
        $columns = Schema::getColumnListing($table);
        $imports = [];
        $cols = [];

        foreach ($columns as $column) {
            if (in_array($column, ['id', 'created_at', 'updated_at', 'deleted_at', 'password', 'remember_token'])) {
                continue;
            }

            if ($column === 'id') {
                $imports[] = 'use App\Services\ResourceSystem\Columns\Column;';
                $cols[] = "Column::make('ID')->sortable()";

                continue;
            }

            if ($column === 'status') {
                $imports[] = 'use App\Services\ResourceSystem\Columns\BadgeColumn;';
                $cols[] = "BadgeColumn::make('Status')->colors([\n                '" . \App\Enums\PublishStatus::PUBLISHED->value . "' => 'success',\n                '" . \App\Enums\PublishStatus::DRAFT->value . "' => 'zinc',\n            ])";

                continue;
            }

            $type = Schema::getColumnType($table, $column);
            $title = Str::of($column)->replace('_', ' ')->title();

            if (Str::contains($column, 'color')) {
                $imports[] = 'use App\Services\ResourceSystem\Columns\ColorColumn;';
                $cols[] = "ColorColumn::make('{$column}')";

                continue;
            }

            switch ($type) {
                case 'boolean':
                    $imports[] = 'use App\Services\ResourceSystem\Columns\BadgeColumn;';
                    $cols[] = "BadgeColumn::make('{$column}')->options([\n                1 => __('general.yes'),\n                0 => __('general.no'),\n            ])";
                    break;
                case 'date':
                    $imports[] = 'use App\Services\ResourceSystem\Columns\DateColumn;';
                    $cols[] = "DateColumn::make('{$column}')";
                    break;
                case 'datetime':
                case 'timestamp':
                    $imports[] = 'use App\Services\ResourceSystem\Columns\DateTimeColumn;';
                    $cols[] = "DateTimeColumn::make('{$column}')";
                    break;
                default:
                    $imports[] = 'use App\Services\ResourceSystem\Columns\Column;';
                    $cols[] = "Column::make('{$column}')->searchable()->sortable()";
                    break;
            }
        }

        return ['imports' => $imports, 'code' => $cols];
    }

    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the resource'],
            ['model', InputArgument::REQUIRED, 'The model class for the resource'],
        ];
    }

    protected function getOptions()
    {
        return [
            ['force', 'f', InputOption::VALUE_NONE, __('commands.make_resource.force_option_description')],
        ];
    }
}
