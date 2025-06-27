<?php

namespace App\Models;

use Database\Factories\FormFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Form extends Model
{
    use HasFactory;
    use HasTranslations;

    protected $fillable = [
        'name',
        'slug',
        'title',
        'description',
        'recipient_email',
        'success_message',
        'send_notification',
        'has_captcha',
        'submit_button_options',
    ];

    public array $translatable = ['title', 'description', 'success_message'];

    protected $casts = [
        'send_notification' => 'boolean',
        'has_captcha' => 'boolean',
        'submit_button_options' => 'array',
    ];

    public function formFields(): HasMany
    {
        return $this->hasMany(FormField::class)->orderBy('sort_order');
    }

    public function fields(): HasMany
    {
        return $this->formFields();
    }

    public function formSubmissions(): HasMany
    {
        return $this->hasMany(FormSubmission::class);
    }

    protected static function newFactory(): FormFactory
    {
        return FormFactory::new();
    }
}
