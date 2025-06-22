<?php

namespace App\Http\Resources\Admin;

use App\Models\Testimonial;
use App\Services\ResourceSystem\Resource;
use App\Services\ResourceSystem\Fields\Text;
use App\Services\ResourceSystem\Fields\Textarea;
use App\Services\ResourceSystem\Fields\Media;
use App\Services\ResourceSystem\Fields\Rating;
use App\Services\ResourceSystem\Columns\Column;
use App\Services\ResourceSystem\Columns\ImageColumn;
use App\Services\ResourceSystem\Columns\RatingColumn;
use App\Services\ResourceSystem\Filters\SelectFilter;

class TestimonialResource extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = Testimonial::class;

    /**
     * The single singular label for the resource.
     *
     * @var string
     */
    public static $singularLabel = 'Testimonial';

    /**
     * The icon to be used in the navigation.
     *
     * @var string
     */
    public static $navigationIcon = 'star';

    /**
     * Get the URI key for the resource.
     *
     * @return string
     */
    public static function uriKey(): string
    {
        return 'testimonials';
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
                ->label('Avatar'),
            Text::make('name')
                ->rules(['required', 'string', 'max:255']),
            Text::make('title')
                ->rules(['nullable', 'string', 'max:255']),
            Textarea::make('content')
                ->rules(['required', 'string']),
            Rating::make('rating')
                ->rules(['required', 'integer', 'min:1', 'max:5'])
                ->default(5),
            Text::make('source')
                ->rules(['nullable', 'string', 'max:255']),
            Text::make('order')
                ->rules(['required', 'integer'])
                ->type('number')
                ->default(0),
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
            Column::make('handle')
                ->label(''),
            ImageColumn::make('avatar')
                ->label('Avatar')
                ->size(40)
                ->circular()
                ->alignment('center'),
            Column::make('name')
                ->sortable()
                ->searchable(),
            RatingColumn::make('rating')
                ->sortable(),
            Column::make('source')
                ->sortable()
                ->searchable(),
            Column::make('order')
                ->sortable(),
        ];
    }

    /**
     * Get the filters available for the resource.
     *
     * @return array
     */
    public function filters(): array
    {
        return [
            SelectFilter::make('rating')
                ->options([
                    '5' => '5 stars',
                    '4' => '4 stars',
                    '3' => '3 stars',
                    '2' => '2 stars',
                    '1' => '1 star',
                ]),
        ];
    }
} 