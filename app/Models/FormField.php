<?php

namespace App\Models;

use App\Enums\FormFieldType;
use Database\Factories\FormFieldFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FormField extends Model implements Sortable
{
    use HasFactory;
    use HasTranslations;
    use SortableTrait;

    protected $fillable = [
        'form_id',
        'type',
        'name',
        'label',
        'placeholder',
        'options',
        'validation_rules',
        'is_required',
        'sort_order',
    ];

    protected $casts = [
        'type' => FormFieldType::class,
    ];

    public array $translatable = ['label', 'placeholder', 'options'];

    public array $sortable = [
        'order_column_name' => 'sort_order',
        'sort_when_creating' => true,
    ];

    public function form(): BelongsTo
    {
        return $this->belongsTo(Form::class);
    }

    protected static function newFactory(): FormFieldFactory
    {
        return FormFieldFactory::new();
    }
}
