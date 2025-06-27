<?php

namespace Tests\Feature\Forms;

use App\Livewire\Forms\FormBuilder;
use App\Livewire\Frontend\FormDisplay;
use App\Models\Form;
use App\Models\User;
use App\Services\SettingsManager;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Tests\TestCase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class FormBuilderTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    public function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);

        $this->user = User::factory()->create();
        $this->user->assignRole('admin');

        Mail::fake();
    }

    /** @test */
    public function can_view_form_builder_page()
    {
        $form = Form::factory()->create();

        $this->actingAs($this->user)
            ->get(route('admin.forms.edit', ['form' => $form]))
            ->assertSuccessful()
            ->assertSee(__('forms.edit_form_title', ['name' => $form->name]));
    }

    /** @test */
    public function can_add_a_text_field_to_a_form()
    {
        $form = Form::factory()->create();

        Livewire::actingAs($this->user)
            ->test(FormBuilder::class, ['form' => $form])
            ->call('addField', 'text')
            ->assertHasNoErrors();

        $this->assertCount(1, $form->refresh()->formFields);
    }

    /** @test */
    public function can_save_form_settings()
    {
        $form = Form::factory()->create(['name' => 'Original Name']);

        Livewire::actingAs($this->user)
            ->test(FormBuilder::class, ['form' => $form])
            ->set('formState.name', 'Updated Name')
            ->call('saveForm')
            ->assertHasNoErrors();

        $this->assertEquals('Updated Name', $form->refresh()->name);
    }

    /** @test */
    public function can_submit_a_form()
    {
        $form = Form::factory()->hasFields(1, ['type' => 'text', 'name' => 'test_input'])->create();

        Livewire::test(FormDisplay::class, ['form' => $form])
            ->set('formData.test_input', 'Hello World')
            ->call('submit')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('form_submissions', [
            'form_id' => $form->id,
            'data->test_input' => 'Hello World',
        ]);
    }

    /** @test */
    public function form_submission_fails_with_invalid_data()
    {
        $form = Form::factory()->hasFields(1, [
            'type' => 'text',
            'name' => 'test_input',
            'is_required' => true,
        ])->create();

        Livewire::test(FormDisplay::class, ['form' => $form])
            ->set('formData.test_input', '')
            ->call('submit')
            ->assertHasErrors(['formData.test_input' => 'required']);
    }

    /** @test */
    public function unauthorized_user_cannot_view_form_builder()
    {
        $user = User::factory()->create();
        $form = Form::factory()->create();

        $this->actingAs($user)->get(route('admin.forms.edit', $form))->assertStatus(403);
    }

    /** @test */
    public function can_remove_a_field_from_a_form()
    {
        $form = Form::factory()->hasFields(1)->create();
        $fieldId = $form->formFields->first()->id;

        Livewire::actingAs($this->user)
            ->test(FormBuilder::class, ['form' => $form])
            ->call('confirmDelete', $fieldId)
            ->call('deleteField', $fieldId);

        $this->assertCount(0, $form->refresh()->formFields);
    }

    /** @test */
    public function can_edit_a_field_in_a_form()
    {
        $form = Form::factory()->hasFields(1, [
            'label' => ['en' => 'Original Label']
        ])->create();
        $field = $form->formFields->first();

        Livewire::actingAs($this->user)
            ->test(FormBuilder::class, ['form' => $form])
            ->call('selectField', $field->id)
            ->set('fieldState.label.en', 'Updated Label')
            ->call('saveField')
            ->assertHasNoErrors()
            ->assertSet('selectedField.label.en', 'Updated Label');

        $this->assertEquals('Updated Label', $form->refresh()->formFields->first()->getTranslation('label', 'en'));
    }

    /** @test */
    public function form_submission_sends_notification_email()
    {
        $form = Form::factory()->hasFields(1)->create(['recipient_email' => 'test@example.com']);
        $field = $form->formFields->first();

        Livewire::test(FormDisplay::class, ['form' => $form])
            ->set('formData.'.$field->name, 'Test')
            ->call('submit');

        Mail::assertSent(\App\Mail\FormSubmissionNotification::class, function ($mail) use ($form) {
            return $mail->hasTo($form->recipient_email);
        });
    }

    /** @test */
    public function can_submit_a_form_with_a_select_field()
    {
        $options = [
            ['value' => 'option_1', 'label' => ['en' => 'Option 1', 'fr' => 'Option 1']],
            ['value' => 'option_2', 'label' => ['en' => 'Option 2', 'fr' => 'Option 2']],
        ];

        $form = Form::factory()->hasFields(1, [
            'type' => 'select',
            'name' => 'selection',
            'options' => $options,
        ])->create();
        $field = $form->formFields->first();

        Livewire::test(FormDisplay::class, ['form' => $form])
            ->set('formData.'.$field->name, 'option_2')
            ->call('submit')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('form_submissions', [
            'form_id' => $form->id,
            'data->selection' => 'option_2',
        ]);
    }

    /** @test */
    public function form_preview_updates_when_field_is_added()
    {
        $form = Form::factory()->create();

        Livewire::actingAs($this->user)
            ->test(FormBuilder::class, ['form' => $form])
            ->call('addField', 'text')
            ->assertSet('form.formFields.0.label', ['en' => 'New text field']);
    }

    /** @test */
    public function form_preview_updates_when_field_is_removed()
    {
        $form = Form::factory()->hasFields(1, [
            'label' => ['en' => 'My Test Field'],
        ])->create();
        $fieldId = $form->formFields->first()->id;

        Livewire::actingAs($this->user)
            ->test(FormBuilder::class, ['form' => $form])
            ->call('removeField', $fieldId)
            ->assertCount('form.formFields', 0);
    }

    /** @test */
    public function can_update_submit_button_text()
    {
        $form = Form::factory()->create();

        Livewire::actingAs($this->user)
            ->test(FormBuilder::class, ['form' => $form])
            ->set('formState.submit_button_options.label.en', 'Send')
            ->call('saveForm');

        $this->assertEquals('Send', $form->refresh()->submit_button_options['label']['en']);
    }

    /** @test */
    public function can_update_field_layout_options()
    {
        $form = Form::factory()->hasFields(1)->create();
        $field = $form->formFields->first();

        Livewire::actingAs($this->user)
            ->test(FormBuilder::class, ['form' => $form])
            ->call('selectField', $field->id)
            ->set('fieldState.layout_options.desktop', '1/2')
            ->call('saveField');

        $this->assertEquals('1/2', $form->refresh()->formFields->first()->layout_options['desktop']);
    }

    /** @test */
    public function unauthorized_user_cannot_edit_a_form()
    {
        $user = User::factory()->create();
        $form = Form::factory()->create();

        Livewire::actingAs($user)
            ->test(FormBuilder::class, ['form' => $form])
            ->set('formState.name', 'Updated Name')
            ->call('saveForm')
            ->assertForbidden();
    }
} 