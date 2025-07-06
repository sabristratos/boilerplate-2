<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model representing a submission to a form.
 *
 * @property int $id
 * @property int $form_id
 * @property array $data
 * @property string $ip_address
 * @property string $user_agent
 *
 * @method BelongsTo form()
 */
class FormSubmission extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'form_id',
        'data',
        'ip_address',
        'user_agent',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'data' => 'array',
    ];

    /**
     * Get the form that owns the submission.
     *
     * @return BelongsTo<Form, FormSubmission>
     */
    public function form(): BelongsTo
    {
        return $this->belongsTo(Form::class);
    }
}
