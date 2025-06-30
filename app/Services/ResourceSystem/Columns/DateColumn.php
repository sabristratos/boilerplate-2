<?php

namespace App\Services\ResourceSystem\Columns;

use Illuminate\Support\Carbon;

class DateColumn extends Column
{
    /**
     * The date format.
     *
     * @var string
     */
    protected $format = 'M j, Y';

    /**
     * Set the date format.
     *
     * @return $this
     */
    public function format(string $format): static
    {
        $this->format = $format;

        return $this;
    }

    /**
     * Format the value.
     *
     * @param  mixed  $value
     * @param  mixed  $resource
     */
    public function formatValue($value, $resource): ?string
    {
        if (is_null($value)) {
            return null;
        }

        return Carbon::parse($value)->format($this->format);
    }
}
