<?php

namespace App\Services\ResourceSystem;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

abstract class Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model;

    /**
     * The single singular label for the resource.
     *
     * @var string
     */
    public static $singularLabel;

    /**
     * The plural label for the resource.
     *
     * @var string
     */
    public static $pluralLabel;

    /**
     * The icon to be used in the navigation.
     * (e.g., from Heroicons)
     *
     * @var string
     */
    public static $navigationIcon = 'heroicon-o-rectangle-stack';

    /**
     * The permission required to view the resource.
     *
     * @var string|null
     */
    public static $permission;

    /**
     * The relationships to eager load.
     *
     * @var array
     */
    public static $with = [];

    /**
     * Whether the resource should be shown in the navigation.
     *
     * @var bool
     */
    public static $showInNavigation = true;

    /**
     * Get the fields displayed by the resource.
     */
    abstract public function fields(): array;

    /**
     * Get the columns displayed by the resource's table.
     */
    abstract public function columns(): array;

    /**
     * Get the filters available for the resource's table.
     */
    public function filters(): array
    {
        return [];
    }

    /**
     * Get the actions available for the resource's table.
     */
    public function actions(): array
    {
        return [];
    }

    /**
     * Get the searchable columns for the resource.
     */
    public function searchableColumns(): array
    {
        return collect($this->columns())
            ->filter(fn ($column) => $column->isSearchable())
            ->map(fn ($column) => $column->getName())
            ->toArray();
    }

    /**
     * Get the sortable columns for the resource.
     */
    public function sortableColumns(): array
    {
        return collect($this->columns())
            ->filter(fn ($column) => $column->isSortable())
            ->map(fn ($column) => $column->getName())
            ->toArray();
    }

    /**
     * Get the URI key for the resource.
     */
    public static function uriKey(): string
    {
        return Str::plural(Str::kebab(class_basename(static::class)));
    }

    /**
     * Get the singular label for the resource.
     */
    public static function singularLabel(): string
    {
        return static::$singularLabel ?? Str::title(Str::singular(class_basename(static::$model)));
    }

    /**
     * Get the plural label for the resource.
     */
    public static function pluralLabel(): string
    {
        return static::$pluralLabel ?? Str::plural(static::singularLabel());
    }

    /**
     * Create a new instance of the resource.
     */
    public static function make(): static
    {
        return new static;
    }

    /**
     * Get a new query builder for the resource's model.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function newQuery()
    {
        return static::$model::with(static::$with);
    }

    /**
     * Get a fresh instance of the model represented by the resource.
     */
    public static function newModel(): Model
    {
        $model = static::$model;

        return new $model;
    }
}
