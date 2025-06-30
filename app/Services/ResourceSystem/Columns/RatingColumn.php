<?php

namespace App\Services\ResourceSystem\Columns;

class RatingColumn extends Column
{
    /**
     * Get the component name for the column.
     */
    public function component(): string
    {
        return 'resource-system::columns.rating-column';
    }
}
