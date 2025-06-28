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
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        'sort_order',
        'validation_rules',
        'placeholder',
        'options',
        'component_options',
        'layout_options',
    ];

    public array $translatable = [
        'label',
        'placeholder',
    ];

    protected $casts = [
        'type' => FormFieldType::class,
        'options' => 'array',
        'component_options' => 'array',
        'layout_options' => 'array',
    ];

    public array $sortable = [
        'order_column_name' => 'sort_order',
        'sort_when_creating' => true,
    ];

    public function form(): BelongsTo
    {
        return $this->belongsTo(Form::class);
    }

    public function options(): HasMany
    {
        return $this->hasMany(FormFieldOption::class)->orderBy('sort_order');
    }

    protected static function newFactory(): FormFieldFactory
    {
        return FormFieldFactory::new();
    }

    public function buildSortQuery()
    {
        return static::query()->where('form_id', $this->form_id);
    }

    public function isRequired(): bool
    {
        if (!$this->validation_rules) {
            return false;
        }

        $rules = explode('|', $this->validation_rules);
        return in_array('required', $rules) || in_array('required_if', $rules);
    }
}
