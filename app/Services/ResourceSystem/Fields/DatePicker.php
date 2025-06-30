<?php

namespace App\Services\ResourceSystem\Fields;

class DatePicker extends Field
{
    /**
     * Get the component name for the field.
     */
    public function component(): string
    {
        return 'resource-system::fields.date-picker';
    }
}
