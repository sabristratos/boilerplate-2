<?php

namespace App\Services\ResourceSystem\Columns;

class DateTimeColumn extends DateColumn
{
    /**
     * The date format.
     *
     * @var string
     */
    protected $format = 'M j, Y, g:i a';
}
