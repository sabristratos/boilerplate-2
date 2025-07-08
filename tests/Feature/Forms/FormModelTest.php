<?php

declare(strict_types=1);

use App\Enums\FormElementType;
use App\Models\Form;
use App\Models\FormSubmission;
use App\Models\User;

beforeEach(function (): void {
    $this->user = User::factory()->create();
    $this->form = Form::factory()->for($this->user)->create();
});

describe('Form Model', function (): void {
    it('can be created with basic attributes', function (): void {
        expect($this->form)
            ->toBeInstanceOf(Form::class)
            ->and($this->form->user_id)->toBe($this->user->id)
            ->and($this->form->getTranslations('name'))->toBeArray()
            ->and($this->form->settings)->toBeNull()
            ->and($this->form->elements)->toBeNull();
    });

    it('has proper relationships', function (): void {
        expect($this->form->user)->toBeInstanceOf(User::class)
            ->and($this->form->user->id)->toBe($this->user->id);

        // Test submissions relationship
        $submission = FormSubmission::factory()->for($this->form)->create();
        expect($this->form->submissions)->toHaveCount(1)
            ->and($this->form->submissions->first())->toBeInstanceOf(FormSubmission::class);
    });

    it('supports translatable name field', function (): void {
        $this->form->setTranslation('name', 'en', 'Contact Form');
        $this->form->setTranslation('name', 'fr', 'Formulaire de Contact');
        $this->form->save();

        expect($this->form->getTranslation('name', 'en'))->toBe('Contact Form')
            ->and($this->form->getTranslation('name', 'fr'))->toBe('Formulaire de Contact');
    });
});

describe('Form Factory', function (): void {
    it('can create forms with different states', function (): void {
        $formWithElements = Form::factory()->create([
            'elements' => [['type' => FormElementType::TEXT->value, 'id' => '1']],
            'settings' => ['backgroundColor' => '#fff'],
        ]);

        expect($formWithElements->elements)->toBe([['type' => FormElementType::TEXT->value, 'id' => '1']])
            ->and($formWithElements->settings)->toBe(['backgroundColor' => '#fff']);
    });
});
