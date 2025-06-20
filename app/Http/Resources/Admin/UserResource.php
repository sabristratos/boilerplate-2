<?php

namespace App\Http\Resources\Admin;

use App\Models\User;
use App\Services\ResourceSystem\Resource;
use App\Services\ResourceSystem\Fields\Text;
use App\Services\ResourceSystem\Fields\Media;
use App\Services\ResourceSystem\Fields\Select;
use App\Services\ResourceSystem\Columns\Column;
use App\Services\ResourceSystem\Columns\BadgeColumn;
use App\Services\ResourceSystem\Columns\ImageColumn;
use App\Services\ResourceSystem\Filters\SelectFilter;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Str;

class UserResource extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = User::class;

    /**
     * The single singular label for the resource.
     *
     * @var string
     */
    public static $singularLabel = 'User';

    /**
     * The icon to be used in the navigation.
     * (e.g., from Heroicons)
     *
     * @var string
     */
    public static $navigationIcon = 'heroicon-o-user';

    /**
     * The permission required to view the resource.
     *
     * @var string
     */
    public static $permission = 'edit users';

    /**
     * The relationships to eager load.
     *
     * @var array
     */
    public static $with = ['roles'];

    /**
     * Get the URI key for the resource.
     *
     * @return string
     */
    public static function uriKey(): string
    {
        return 'users';
    }

    /**
     * Get the fields displayed by the resource.
     *
     * @return array
     */
    public function fields(): array
    {
        return [
            Media::make('avatar')
                ->label('Avatar')
                ->helpText('Upload a user avatar. Recommended size is 200x200px.'),

            Text::make('name')
                ->rules(['required', 'max:255']),

            Text::make('email')
                ->rules(['required', 'email', 'max:255']),

            Text::make('password')
                ->rules(['sometimes', 'required', 'min:8'])
                ->type('password')
                ->helpText('Leave blank to keep current password.'),

            Select::make('roles')
                ->options(
                    Role::all()->mapWithKeys(function ($role) {
                        return [$role->name => Str::title(str_replace(['-', '_'], ' ', $role->name))];
                    })->toArray()
                )
                ->rules(['sometimes', 'array'])
                ->multiple()
                ->default(['user'])
                ->placeholder('Select roles'),
        ];
    }

    /**
     * Get the columns displayed by the resource's table.
     *
     * @return array
     */
    public function columns(): array
    {
        return [
            ImageColumn::make('avatar')
                ->label('Avatar')
                ->size(40)
                ->circular()
                ->alignment('center'),

            Column::make('id')
                ->sortable(),

            Column::make('name')
                ->sortable()
                ->searchable(),

            Column::make('email')
                ->sortable()
                ->searchable(),

            Column::make('roles')
                ->setFormatValueCallback(function ($value, $resource) {
                    return $resource->roles->pluck('name')->map(fn ($name) => Str::title(str_replace('-', ' ', $name)))->implode(', ');
                }),

            BadgeColumn::make('email_verified_at')
                ->label('Verified')
                ->colors([
                    'verified' => 'success',
                    'unverified' => 'danger',
                ])
                ->setFormatValueCallback(function ($value, $resource) {
                    return $value ? 'verified' : 'unverified';
                }),

            Column::make('created_at')
                ->sortable()
                ->label('Created'),
        ];
    }

    /**
     * Get the filters available for the resource's table.
     *
     * @return array
     */
    public function filters(): array
    {
        return [
            SelectFilter::make('verified')
                ->options([
                    'verified' => 'Verified',
                    'unverified' => 'Unverified',
                ])
                ->setApplyCallback(function ($query, $value) {
                    if ($value === 'verified') {
                        return $query->whereNotNull('email_verified_at');
                    } elseif ($value === 'unverified') {
                        return $query->whereNull('email_verified_at');
                    }
                    return $query;
                }),
        ];
    }
}
