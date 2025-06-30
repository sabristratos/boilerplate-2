<?php

namespace App\Services\ResourceSystem\Fields;

class Rating extends Field
{
    /**
     * The component to be used for the field.
     *
     * @var string
     */
    public function component(): string
    {
        return 'rating';
    }
}
