<?php

namespace App\Http\Resources\Admin;

use App\Models\Testimonial;
use App\Services\ResourceSystem\Columns\Column;
use App\Services\ResourceSystem\Columns\ImageColumn;
use App\Services\ResourceSystem\Columns\RatingColumn;
use App\Services\ResourceSystem\Fields\Media;
use App\Services\ResourceSystem\Fields\Rating;
use App\Services\ResourceSystem\Fields\Text;
use App\Services\ResourceSystem\Fields\Textarea;
use App\Services\ResourceSystem\Filters\SelectFilter;
use App\Services\ResourceSystem\Resource;

class TestimonialResource extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = Testimonial::class;

    /**
     * The relationships to eager load.
     *
     * @var array
     */
    public static $with = ['media'];

    /**
     * The icon to be used in the navigation.
     *
     * @var string
     */
    public static $navigationIcon = 'star';

    /**
     * Get the URI key for the resource.
     */
    public static function uriKey(): string
    {
        return 'testimonials';
    }

    /**
     * Get the singular label for the resource.
     */
    public static function singularLabel(): string
    {
        return __('resources.testimonial');
    }

    /**
     * Get the plural label for the resource.
     */
    public static function pluralLabel(): string
    {
        return __('resources.testimonials');
    }

    /**
     * Get the fields displayed by the resource.
     */
    public function fields(): array
    {
        return [
            Media::make('avatar')
                ->label(__('labels.avatar')),
            Text::make('name')
                ->label(__('labels.name'))
                ->rules(['required', 'string', 'max:255']),
            Text::make('title')
                ->label(__('labels.title'))
                ->rules(['nullable', 'string', 'max:255']),
            Textarea::make('content')
                ->label(__('labels.content'))
                ->rules(['required', 'string']),
            Rating::make('rating')
                ->label(__('labels.rating'))
                ->rules(['required', 'integer', 'min:1', 'max:5'])
                ->default(5),
            Text::make('source')
                ->label(__('labels.source'))
                ->rules(['nullable', 'string', 'max:255']),
            Text::make('order')
                ->label(__('labels.order'))
                ->rules(['required', 'integer'])
                ->type('number')
                ->default(0),
        ];
    }

    /**
     * Get the columns displayed by the resource's table.
     */
    public function columns(): array
    {
        return [
            Column::make('handle')
                ->label(''),
            ImageColumn::make('avatar')
                ->label(__('labels.avatar'))
                ->size(40)
                ->circular()
                ->alignment('center'),
            Column::make('name')
                ->label(__('labels.name'))
                ->sortable()
                ->searchable(),
            RatingColumn::make('rating')
                ->label(__('labels.rating'))
                ->sortable(),
            Column::make('source')
                ->label(__('labels.source'))
                ->sortable()
                ->searchable(),
            Column::make('order')
                ->label(__('labels.order'))
                ->sortable(),
        ];
    }

    /**
     * Get the filters available for the resource.
     */
    public function filters(): array
    {
        return [
            SelectFilter::make('rating')
                ->label(__('labels.rating'))
                ->options([
                    '5' => __('labels.5_stars'),
                    '4' => __('labels.4_stars'),
                    '3' => __('labels.3_stars'),
                    '2' => __('labels.2_stars'),
                    '1' => __('resources.labels.1_star'),
                ]),
        ];
    }
}
