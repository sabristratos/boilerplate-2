<?php

declare(strict_types=1);

use App\Actions\Forms\DiscardFormDraftAction;
use App\Actions\Forms\PublishFormAction;
use App\Actions\Forms\SaveDraftFormAction;
use App\Enums\FormElementType;
use App\Models\Form;
use App\Models\User;

beforeEach(function (): void {
    $this->user = User::factory()->create();
    $this->form = Form::factory()->for($this->user)->create();
    $this->saveDraftAction = new SaveDraftFormAction;
    $this->publishAction = new PublishFormAction;
    $this->discardAction = new DiscardFormDraftAction;
});

describe('SaveDraftFormAction', function (): void {
    it('can save draft form data', function (): void {
        $elements = [['type' => FormElementType::TEXT->value, 'id' => '1', 'properties' => ['label' => 'Name']]];
        $settings = ['backgroundColor' => '#fff', 'defaultFont' => 'Inter'];
        $name = ['en' => 'Contact Form', 'fr' => 'Formulaire de Contact'];
        $locale = 'en';

        $updatedForm = $this->saveDraftAction->execute(
            $this->form,
            $elements,
            $settings,
            $name,
            $locale
        );

        expect($updatedForm->draft_elements)->toBe($elements)
            ->and($updatedForm->draft_settings)->toBe($settings)
            ->and($updatedForm->getTranslation('draft_name', 'en'))->toBe('Contact Form')
            ->and($updatedForm->getTranslation('draft_name', 'fr'))->toBe('Formulaire de Contact')
            ->and($updatedForm->last_draft_at)->not->toBeNull();
    });

    it('can save draft data with empty name translations', function (): void {
        $elements = [['type' => FormElementType::EMAIL->value, 'id' => '1']];
        $settings = ['backgroundColor' => '#000'];
        $name = [];
        $locale = 'en';

        $updatedForm = $this->saveDraftAction->execute(
            $this->form,
            $elements,
            $settings,
            $name,
            $locale
        );

        expect($updatedForm->draft_elements)->toBe($elements)
            ->and($updatedForm->draft_settings)->toBe($settings)
            ->and($updatedForm->last_draft_at)->not->toBeNull();
    });

    it('can save draft data with partial name translations', function (): void {
        $elements = [['type' => FormElementType::TEXTAREA->value, 'id' => '1']];
        $settings = ['backgroundColor' => '#fff'];
        $name = ['en' => 'Contact Form']; // Only English
        $locale = 'en';

        $updatedForm = $this->saveDraftAction->execute(
            $this->form,
            $elements,
            $settings,
            $name,
            $locale
        );

        expect($updatedForm->getTranslation('draft_name', 'en'))->toBe('Contact Form')
            ->and($updatedForm->getTranslation('draft_name', 'fr'))->toBe('Contact Form'); // fallback
    });

    it('updates last_draft_at timestamp', function (): void {
        $originalTime = $this->form->last_draft_at;

        $updatedForm = $this->saveDraftAction->execute(
            $this->form,
            [],
            [],
            [],
            'en'
        );

        expect($updatedForm->last_draft_at)->not->toBe($originalTime)
            ->and($updatedForm->last_draft_at)->toBeInstanceOf(\Carbon\Carbon::class);
    });
});

describe('PublishFormAction', function (): void {
    it('can publish draft changes to published fields', function (): void {
        // Set up draft data
        $draftName = ['en' => 'Draft Form'];
        $draftElements = [['type' => FormElementType::TEXT->value, 'id' => '1']];
        $draftSettings = ['backgroundColor' => '#fff'];

        $this->form->draft_name = $draftName;
        $this->form->draft_elements = $draftElements;
        $this->form->draft_settings = $draftSettings;
        $this->form->last_draft_at = now();
        $this->form->save();

        $updatedForm = $this->publishAction->execute($this->form);

        expect($updatedForm->getTranslation('name', 'en'))->toBe('Draft Form')
            ->and($updatedForm->elements)->toBe($draftElements)
            ->and($updatedForm->settings)->toBe($draftSettings)
            ->and(($updatedForm->draft_name === null || $updatedForm->draft_name === ''))->toBeTrue()
            ->and($updatedForm->draft_elements)->toBeNull()
            ->and($updatedForm->draft_settings)->toBeNull()
            ->and($updatedForm->last_draft_at)->toBeNull();
    });

    it('does not publish when no draft changes exist', function (): void {
        $originalName = $this->form->name;
        $originalElements = $this->form->elements;
        $originalSettings = $this->form->settings;

        $updatedForm = $this->publishAction->execute($this->form);

        expect($updatedForm->name)->toBe($originalName)
            ->and($updatedForm->elements)->toBe($originalElements)
            ->and($updatedForm->settings)->toBe($originalSettings);
    });

    it('can publish partial draft changes', function (): void {
        $draftName = ['en' => 'Draft Form'];
        $this->form->draft_name = $draftName;
        $this->form->save();

        $updatedForm = $this->publishAction->execute($this->form);

        expect($updatedForm->getTranslation('name', 'en'))->toBe('Draft Form')
            ->and(($updatedForm->draft_name === null || $updatedForm->draft_name === ''))->toBeTrue()
            ->and($updatedForm->draft_elements)->toBeNull()
            ->and($updatedForm->draft_settings)->toBeNull();
    });

    it('preserves existing published data when no draft exists for that field', function (): void {
        $publishedName = ['en' => 'Published Form'];
        $publishedElements = [['type' => FormElementType::TEXT->value, 'id' => '1']];
        $publishedSettings = ['backgroundColor' => '#fff'];
        $draftElements = [['type' => FormElementType::EMAIL->value, 'id' => '2']];

        $this->form->name = $publishedName;
        $this->form->elements = $publishedElements;
        $this->form->settings = $publishedSettings;
        $this->form->draft_elements = $draftElements;
        $this->form->save();

        $updatedForm = $this->publishAction->execute($this->form);

        expect($updatedForm->getTranslation('name', 'en'))->toBe('Published Form') // Should preserve published name
            ->and($updatedForm->elements)->toMatchArray($draftElements) // Should use draft elements
            ->and($updatedForm->settings)->toBe($publishedSettings); // Should preserve published settings
    });
});

describe('DiscardFormDraftAction', function (): void {
    it('can discard all draft changes', function (): void {
        // Set up draft data
        $this->form->draft_name = ['en' => 'Draft Form'];
        $this->form->draft_elements = [['type' => FormElementType::TEXT->value, 'id' => '1']];
        $this->form->draft_settings = ['backgroundColor' => '#fff'];
        $this->form->last_draft_at = now();
        $this->form->save();

        $updatedForm = $this->discardAction->execute($this->form);

        expect(($updatedForm->draft_name === null || $updatedForm->draft_name === ''))->toBeTrue()
            ->and($updatedForm->draft_elements)->toBeNull()
            ->and($updatedForm->draft_settings)->toBeNull()
            ->and($updatedForm->last_draft_at)->toBeNull();
    });

    it('does not affect published data when discarding drafts', function (): void {
        // Set up published data
        $publishedName = ['en' => 'Published Form'];
        $publishedElements = [['type' => FormElementType::EMAIL->value, 'id' => '1']];
        $publishedSettings = ['backgroundColor' => '#000'];

        $this->form->name = $publishedName;
        $this->form->elements = $publishedElements;
        $this->form->settings = $publishedSettings;
        $this->form->save();

        // Set up draft data
        $this->form->draft_name = ['en' => 'Draft Form'];
        $this->form->draft_elements = [['type' => FormElementType::TEXT->value, 'id' => '2']];
        $this->form->draft_settings = ['backgroundColor' => '#fff'];
        $this->form->last_draft_at = now();
        $this->form->save();

        $updatedForm = $this->discardAction->execute($this->form);

        // Published data should remain unchanged
        expect($updatedForm->getTranslation('name', 'en'))->toBe('Published Form')
            ->and($updatedForm->elements)->toBe($publishedElements)
            ->and($updatedForm->settings)->toBe($publishedSettings);

        // Draft data should be cleared
        expect(($updatedForm->draft_name === null || $updatedForm->draft_name === ''))->toBeTrue()
            ->and($updatedForm->draft_elements)->toBeNull()
            ->and($updatedForm->draft_settings)->toBeNull();
    });

    it('can discard drafts when no draft data exists', function (): void {
        $updatedForm = $this->discardAction->execute($this->form);

        expect(($updatedForm->draft_name === null || $updatedForm->draft_name === ''))->toBeTrue()
            ->and($updatedForm->draft_elements)->toBeNull()
            ->and($updatedForm->draft_settings)->toBeNull()
            ->and($updatedForm->last_draft_at)->toBeNull();
    });
});

describe('Form Actions Integration', function (): void {
    it('can perform a complete draft workflow', function (): void {
        // 1. Save draft
        $elements = [['type' => FormElementType::TEXT->value, 'id' => '1', 'properties' => ['label' => 'Name']]];
        $settings = ['backgroundColor' => '#fff'];
        $name = ['en' => 'Contact Form'];

        $formWithDraft = $this->saveDraftAction->execute(
            $this->form,
            $elements,
            $settings,
            $name,
            'en'
        );

        expect($formWithDraft->hasDraftChanges())->toBeTrue();

        // 2. Publish draft
        $publishedForm = $this->publishAction->execute($formWithDraft);

        expect($publishedForm->hasDraftChanges())->toBeFalse()
            ->and($publishedForm->getTranslation('name', 'en'))->toBe('Contact Form')
            ->and($publishedForm->elements)->toBe($elements)
            ->and($publishedForm->settings)->toBe($settings);

        // 3. Create new draft
        $newElements = [['type' => FormElementType::EMAIL->value, 'id' => '2', 'properties' => ['label' => 'Email']]];
        $newSettings = ['backgroundColor' => '#000'];
        $newName = ['en' => 'Updated Contact Form'];

        $formWithNewDraft = $this->saveDraftAction->execute(
            $publishedForm,
            $newElements,
            $newSettings,
            $newName,
            'en'
        );

        expect($formWithNewDraft->hasDraftChanges())->toBeTrue();

        // 4. Discard new draft
        $formAfterDiscard = $this->discardAction->execute($formWithNewDraft);

        expect($formAfterDiscard->hasDraftChanges())->toBeFalse()
            ->and($formAfterDiscard->getTranslation('name', 'en'))->toBe('Contact Form') // Should still have original published name
            ->and($formAfterDiscard->elements)->toBe($elements) // Should still have original published elements
            ->and($formAfterDiscard->settings)->toBe($settings); // Should still have original published settings
    });
});
