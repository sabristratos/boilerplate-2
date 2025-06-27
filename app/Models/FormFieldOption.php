<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FormFieldOption extends Model implements Sortable
{
    use HasFactory;
    use HasTranslations;
    use SortableTrait;

    public $timestamps = false;

    protected $fillable = [
        'form_field_id',
        'label',
        'value',
        'sort_order',
    ];

    public array $translatable = [
        'label',
    ];

    protected $casts = [
        'label' => 'array',
    ];

    public array $sortable = [
        'order_column_name' => 'sort_order',
        'sort_when_creating' => true,
    ];

    public function formField(): BelongsTo
    {
        return $this->belongsTo(FormField::class);
    }

    public function buildSortQuery()
    {
        return static::query()->where('form_field_id', $this->form_field_id);
    }
} 