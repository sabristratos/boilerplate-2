<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Translation extends Model
{
    use HasTranslations;

    public $translatable = ['text'];

    protected $fillable = [
        'group',
        'key',
        'text',
    ];

    protected function casts(): array
    {
        return [
            'text' => 'translatable',
        ];
    }
}
