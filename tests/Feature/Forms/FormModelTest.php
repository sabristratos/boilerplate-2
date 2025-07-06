<?php

declare(strict_types=1);

use App\Models\Form;
use App\Models\User;
use App\Models\FormSubmission;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->form = Form::factory()->for($this->user)->create();
});

describe('Form Model', function () {
    it('can be created with basic attributes', function () {
        expect($this->form)
            ->toBeInstanceOf(Form::class)
            ->and($this->form->user_id)->toBe($this->user->id)
            ->and($this->form->getTranslations('name'))->toBeArray()
            ->and($this->form->settings)->toBeNull()
            ->and($this->form->elements)->toBeNull();
    });

    it('has proper relationships', function () {
        expect($this->form->user)->toBeInstanceOf(User::class)
            ->and($this->form->user->id)->toBe($this->user->id);

        // Test submissions relationship
        $submission = FormSubmission::factory()->for($this->form)->create();
        expect($this->form->submissions)->toHaveCount(1)
            ->and($this->form->submissions->first())->toBeInstanceOf(FormSubmission::class);
    });

    it('supports translatable name field', function () {
        $this->form->setTranslation('name', 'en', 'Contact Form');
        $this->form->setTranslation('name', 'fr', 'Formulaire de Contact');
        $this->form->save();

        expect($this->form->getTranslation('name', 'en'))->toBe('Contact Form')
            ->and($this->form->getTranslation('name', 'fr'))->toBe('Formulaire de Contact');
    });
});

describe('Form Draft Functionality', function () {
    it('can check if it has draft changes', function () {
        expect($this->form->hasDraftChanges())->toBeFalse();

        $this->form->draft_name = ['en' => 'Draft Name'];
        expect($this->form->hasDraftChanges())->toBeTrue();

        $this->form->draft_name = null;
        $this->form->draft_elements = [['type' => 'text']];
        expect($this->form->hasDraftChanges())->toBeTrue();

        $this->form->draft_elements = null;
        $this->form->draft_settings = ['backgroundColor' => '#fff'];
        expect($this->form->hasDraftChanges())->toBeTrue();
    });

    it('can get current elements (draft or published)', function () {
        $publishedElements = [['type' => 'text', 'id' => '1']];
        $draftElements = [['type' => 'email', 'id' => '2']];

        // Test with no draft - should return published
        $this->form->elements = $publishedElements;
        $this->form->save();
        expect($this->form->getCurrentElements())->toBe($publishedElements);

        // Test with draft - should return draft
        $this->form->draft_elements = $draftElements;
        $this->form->save();
        expect($this->form->getCurrentElements())->toBe($draftElements);
    });

    it('can get current settings (draft or published)', function () {
        $publishedSettings = ['backgroundColor' => '#fff'];
        $draftSettings = ['backgroundColor' => '#000'];

        // Test with no draft - should return published
        $this->form->settings = $publishedSettings;
        $this->form->save();
        expect($this->form->getCurrentSettings())->toBe($publishedSettings);

        // Test with draft - should return draft
        $this->form->draft_settings = $draftSettings;
        $this->form->save();
        expect($this->form->getCurrentSettings())->toBe($draftSettings);
    });

    it('can get current name (draft or published)', function () {
        $this->form->setTranslation('name', 'en', 'Published Name');
        $this->form->setTranslation('draft_name', 'en', 'Draft Name');
        $this->form->save();

        // Test with locale parameter
        expect($this->form->getCurrentName('en'))->toBe(['en' => 'Draft Name']);

        // Test without locale parameter
        expect($this->form->getCurrentName())->toBe(['en' => 'Draft Name']);

        // Test with no draft name
        $this->form->draft_name = null;
        $this->form->save();
        expect($this->form->getCurrentName('en'))->toBe(['en' => 'Published Name']);
    });

    it('can publish draft changes', function () {
        $draftName = ['en' => 'Draft Name'];
        $draftElements = [['type' => 'text', 'id' => '1']];
        $draftSettings = ['backgroundColor' => '#fff'];

        $this->form->draft_name = $draftName;
        $this->form->draft_elements = $draftElements;
        $this->form->draft_settings = $draftSettings;
        $this->form->last_draft_at = now();
        $this->form->save();

        $this->form->publishDraft();

        expect($this->form->getTranslation('name', 'en'))->toBe('Draft Name')
            ->and($this->form->elements)->toBe($draftElements)
            ->and($this->form->settings)->toBe($draftSettings)
            ->and(($this->form->draft_name === null || $this->form->draft_name === ''))->toBeTrue()
            ->and($this->form->draft_elements)->toBeNull()
            ->and($this->form->draft_settings)->toBeNull()
            ->and($this->form->last_draft_at)->toBeNull();
    });

    it('can discard draft changes', function () {
        $this->form->draft_name = ['en' => 'Draft Name'];
        $this->form->draft_elements = [['type' => 'text']];
        $this->form->draft_settings = ['backgroundColor' => '#fff'];
        $this->form->last_draft_at = now();
        $this->form->save();

        $this->form->discardDraft();

        expect(($this->form->draft_name === null || $this->form->draft_name === ''))->toBeTrue()
            ->and($this->form->draft_elements)->toBeNull()
            ->and($this->form->draft_settings)->toBeNull()
            ->and($this->form->last_draft_at)->toBeNull();
    });

    it('does not publish when no draft changes exist', function () {
        $originalName = $this->form->name;
        $originalElements = $this->form->elements;
        $originalSettings = $this->form->settings;

        $this->form->publishDraft();

        expect($this->form->name)->toBe($originalName)
            ->and($this->form->elements)->toBe($originalElements)
            ->and($this->form->settings)->toBe($originalSettings);
    });
});

describe('Form Factory', function () {
    it('can create forms with different states', function () {
        $formWithElements = Form::factory()->create([
            'elements' => [['type' => 'text', 'id' => '1']],
            'settings' => ['backgroundColor' => '#fff'],
        ]);

        expect($formWithElements->elements)->toBe([['type' => 'text', 'id' => '1']])
            ->and($formWithElements->settings)->toBe(['backgroundColor' => '#fff']);
    });

    it('can create forms with draft data', function () {
        $formWithDraft = Form::factory()->create([
            'draft_name' => ['en' => 'Draft Form'],
            'draft_elements' => [['type' => 'email', 'id' => '1']],
            'draft_settings' => ['backgroundColor' => '#000'],
        ]);

        expect($formWithDraft->hasDraftChanges())->toBeTrue()
            ->and($formWithDraft->getCurrentElements())->toBe([['type' => 'email', 'id' => '1']])
            ->and($formWithDraft->getCurrentSettings())->toBe(['backgroundColor' => '#000']);
    });
}); 