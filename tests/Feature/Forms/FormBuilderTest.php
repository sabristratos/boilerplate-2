<?php

namespace Tests\Feature\Forms;

use App\Livewire\FormBuilder;
use App\Models\Form;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class FormBuilderTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /** @test */
    public function form_builder_renders_successfully()
    {
        $form = Form::factory()->create(['user_id' => $this->user->id]);

        Livewire::actingAs($this->user)
            ->test(FormBuilder::class, ['form' => $form])
            ->assertStatus(200);
    }

    /** @test */
    public function form_builder_can_add_text_element()
    {
        $form = Form::factory()->create(['user_id' => $this->user->id]);

        Livewire::actingAs($this->user)
            ->test(FormBuilder::class, ['form' => $form])
            ->assertSet('elements', [])
            ->call('addElement', 'text')
            ->assertCount('elements', 1)
            ->assertSet('elements.0.type', 'text');
    }

    /** @test */
    public function form_builder_can_add_multiple_element_types()
    {
        $form = Form::factory()->create(['user_id' => $this->user->id]);

        Livewire::actingAs($this->user)
            ->test(FormBuilder::class, ['form' => $form])
            ->call('addElement', 'text')
            ->call('addElement', 'email')
            ->call('addElement', 'textarea')
            ->call('addElement', 'select')
            ->call('addElement', 'checkbox')
            ->call('addElement', 'radio')
            ->call('addElement', 'date')
            ->call('addElement', 'number')
            ->call('addElement', 'file')
            ->assertCount('elements', 9);
    }

    /** @test */
    public function form_builder_can_configure_element_properties()
    {
        $form = Form::factory()->create(['user_id' => $this->user->id]);

        Livewire::actingAs($this->user)
            ->test(FormBuilder::class, ['form' => $form])
            ->call('addElement', 'text')
            ->set('elements.0.properties.label', 'Full Name')
            ->set('elements.0.properties.placeholder', 'Enter your full name')
            ->set('elements.0.properties.required', true)
            ->assertSet('elements.0.properties.label', 'Full Name')
            ->assertSet('elements.0.properties.placeholder', 'Enter your full name')
            ->assertSet('elements.0.properties.required', true);
    }

    /** @test */
    public function form_builder_can_configure_element_validation()
    {
        $form = Form::factory()->create(['user_id' => $this->user->id]);

        Livewire::actingAs($this->user)
            ->test(FormBuilder::class, ['form' => $form])
            ->call('addElement', 'email')
            ->set('elements.0.properties.label', 'Email Address')
            ->set('elements.0.validation.rules', ['required', 'email'])
            ->assertSet('elements.0.validation.rules', ['required', 'email']);
    }

    /** @test */
    public function form_builder_can_configure_element_styles()
    {
        $form = Form::factory()->create(['user_id' => $this->user->id]);

        Livewire::actingAs($this->user)
            ->test(FormBuilder::class, ['form' => $form])
            ->call('addElement', 'text')
            ->set('elements.0.styles.desktop.width', '50%')
            ->set('elements.0.styles.desktop.margin', '10px')
            ->set('elements.0.styles.tablet.width', '75%')
            ->set('elements.0.styles.mobile.width', '100%')
            ->assertSet('elements.0.styles.desktop.width', '50%')
            ->assertSet('elements.0.styles.tablet.width', '75%')
            ->assertSet('elements.0.styles.mobile.width', '100%');
    }

    /** @test */
    public function form_builder_can_select_and_edit_elements()
    {
        $form = Form::factory()->create([
            'user_id' => $this->user->id,
            'elements' => [
                [
                    'id' => 'element-1',
                    'type' => 'text',
                    'properties' => ['label' => 'Old Label'],
                    'styles' => ['desktop' => [], 'tablet' => [], 'mobile' => []],
                ],
            ],
        ]);

        Livewire::actingAs($this->user)
            ->test(FormBuilder::class, ['form' => $form])
            ->set('selectedElementId', 'element-1')
            ->assertSet('selectedElementId', 'element-1')
            ->set('elements.0.properties.label', 'New Label')
            ->assertSet('elements.0.properties.label', 'New Label');
    }

    /** @test */
    public function form_builder_can_delete_elements()
    {
        $form = Form::factory()->create([
            'user_id' => $this->user->id,
            'elements' => [
                [
                    'id' => 'element-1',
                    'type' => 'text',
                    'properties' => [
                        'label' => 'Test Field',
                        'placeholder' => '',
                        'fluxProps' => ['clearable' => false, 'copyable' => false, 'viewable' => false],
                    ],
                    'styles' => ['desktop' => [], 'tablet' => [], 'mobile' => []],
                ],
            ],
        ]);

        Livewire::actingAs($this->user)
            ->test(FormBuilder::class, ['form' => $form])
            ->assertCount('elements', 1)
            ->call('deleteElement', 'element-1')
            ->assertCount('elements', 0)
            ->assertSet('selectedElementId', null);
    }

    /** @test */
    public function form_builder_can_reorder_elements()
    {
        $form = Form::factory()->create([
            'user_id' => $this->user->id,
            'elements' => [
                [
                    'id' => 'element-1',
                    'type' => 'text',
                    'properties' => ['label' => 'First'],
                    'styles' => ['desktop' => [], 'tablet' => [], 'mobile' => []],
                ],
                [
                    'id' => 'element-2',
                    'type' => 'email',
                    'properties' => ['label' => 'Second'],
                    'styles' => ['desktop' => [], 'tablet' => [], 'mobile' => []],
                ],
            ],
        ]);

        Livewire::actingAs($this->user)
            ->test(FormBuilder::class, ['form' => $form])
            ->assertSet('elements.0.id', 'element-1')
            ->assertSet('elements.1.id', 'element-2')
            ->call('handleReorder', ['element-2', 'element-1'])
            ->assertSet('elements.0.id', 'element-2')
            ->assertSet('elements.1.id', 'element-1');
    }

    /** @test */
    public function form_builder_can_configure_form_settings()
    {
        $form = Form::factory()->create(['user_id' => $this->user->id]);

        Livewire::actingAs($this->user)
            ->test(FormBuilder::class, ['form' => $form])
            ->set('settings.backgroundColor', '#ffffff')
            ->set('settings.textColor', '#000000')
            ->set('settings.submitButtonText', 'Send Message')
            ->set('settings.successMessage', 'Thank you for your submission!')
            ->set('settings.enabled', true)
            ->assertSet('settings.backgroundColor', '#ffffff')
            ->assertSet('settings.textColor', '#000000')
            ->assertSet('settings.submitButtonText', 'Send Message')
            ->assertSet('settings.successMessage', 'Thank you for your submission!')
            ->assertSet('settings.enabled', true);
    }

    /** @test */
    public function form_builder_can_save_form()
    {
        $form = Form::factory()->create([
            'user_id' => $this->user->id,
            'settings' => ['backgroundColor' => '#ffffff'],
        ]);

        Livewire::actingAs($this->user)
            ->test(FormBuilder::class, ['form' => $form])
            ->call('addElement', 'text')
            ->set('elements.0.properties.label', 'Name')
            ->set('settings.backgroundColor', '#000000')
            ->call('save')
            ->assertHasNoErrors();

        $form->refresh();
        expect($form->elements)->toHaveCount(1);
        expect($form->elements[0]['properties']['label'])->toBe('Name');
        expect($form->settings['backgroundColor'])->toBe('#000000');
    }

    /** @test */
    public function form_builder_validates_required_fields()
    {
        $form = Form::factory()->create(['user_id' => $this->user->id]);

        Livewire::actingAs($this->user)
            ->test(FormBuilder::class, ['form' => $form])
            ->call('addElement', 'text')
            ->set('elements.0.properties.label', '') // Empty label
            ->call('save')
            ->assertHasErrors(['elements.0.properties.label']);
    }

    /** @test */
    public function form_builder_handles_breakpoint_changes()
    {
        $form = Form::factory()->create(['user_id' => $this->user->id]);

        Livewire::actingAs($this->user)
            ->test(FormBuilder::class, ['form' => $form])
            ->call('addElement', 'text')
            ->set('currentBreakpoint', 'tablet')
            ->assertSet('currentBreakpoint', 'tablet')
            ->set('currentBreakpoint', 'mobile')
            ->assertSet('currentBreakpoint', 'mobile')
            ->set('currentBreakpoint', 'desktop')
            ->assertSet('currentBreakpoint', 'desktop');
    }

    /** @test */
    public function form_builder_can_duplicate_elements()
    {
        $form = Form::factory()->create([
            'user_id' => $this->user->id,
            'elements' => [
                [
                    'id' => 'element-1',
                    'type' => 'text',
                    'properties' => ['label' => 'Original Field'],
                    'styles' => ['desktop' => [], 'tablet' => [], 'mobile' => []],
                ],
            ],
        ]);

        Livewire::actingAs($this->user)
            ->test(FormBuilder::class, ['form' => $form])
            ->assertCount('elements', 1)
            ->call('duplicateElement', 'element-1')
            ->assertCount('elements', 2)
            ->assertSet('elements.1.properties.label', 'Original Field');
    }

    /** @test */
    public function form_builder_can_configure_select_options()
    {
        $form = Form::factory()->create(['user_id' => $this->user->id]);

        Livewire::actingAs($this->user)
            ->test(FormBuilder::class, ['form' => $form])
            ->call('addElement', 'select')
            ->set('elements.0.properties.label', 'Country')
            ->set('elements.0.properties.options', [
                ['label' => 'United States', 'value' => 'us'],
                ['label' => 'Canada', 'value' => 'ca'],
                ['label' => 'United Kingdom', 'value' => 'uk'],
            ])
            ->assertSet('elements.0.properties.options.0.label', 'United States')
            ->assertSet('elements.0.properties.options.1.label', 'Canada')
            ->assertSet('elements.0.properties.options.2.label', 'United Kingdom');
    }

    /** @test */
    public function form_builder_can_configure_file_upload_settings()
    {
        $form = Form::factory()->create(['user_id' => $this->user->id]);

        Livewire::actingAs($this->user)
            ->test(FormBuilder::class, ['form' => $form])
            ->call('addElement', 'file')
            ->set('elements.0.properties.label', 'Document')
            ->set('elements.0.properties.accept', '.pdf,.doc,.docx')
            ->set('elements.0.properties.maxSize', '5MB')
            ->assertSet('elements.0.properties.accept', '.pdf,.doc,.docx')
            ->assertSet('elements.0.properties.maxSize', '5MB');
    }

    /** @test */
    public function form_builder_can_configure_checkbox_options()
    {
        $form = Form::factory()->create(['user_id' => $this->user->id]);

        Livewire::actingAs($this->user)
            ->test(FormBuilder::class, ['form' => $form])
            ->call('addElement', 'checkbox')
            ->set('elements.0.properties.label', 'Interests')
            ->set('elements.0.properties.options', [
                ['label' => 'Technology', 'value' => 'tech'],
                ['label' => 'Sports', 'value' => 'sports'],
                ['label' => 'Music', 'value' => 'music'],
            ])
            ->assertSet('elements.0.properties.options.0.label', 'Technology')
            ->assertSet('elements.0.properties.options.1.label', 'Sports')
            ->assertSet('elements.0.properties.options.2.label', 'Music');
    }

    /** @test */
    public function form_builder_can_configure_radio_options()
    {
        $form = Form::factory()->create(['user_id' => $this->user->id]);

        Livewire::actingAs($this->user)
            ->test(FormBuilder::class, ['form' => $form])
            ->call('addElement', 'radio')
            ->set('elements.0.properties.label', 'Gender')
            ->set('elements.0.properties.options', [
                ['label' => 'Male', 'value' => 'male'],
                ['label' => 'Female', 'value' => 'female'],
                ['label' => 'Other', 'value' => 'other'],
            ])
            ->assertSet('elements.0.properties.options.0.label', 'Male')
            ->assertSet('elements.0.properties.options.1.label', 'Female')
            ->assertSet('elements.0.properties.options.2.label', 'Other');
    }

    /** @test */
    public function form_builder_can_configure_date_field_settings()
    {
        $form = Form::factory()->create(['user_id' => $this->user->id]);

        Livewire::actingAs($this->user)
            ->test(FormBuilder::class, ['form' => $form])
            ->call('addElement', 'date')
            ->set('elements.0.properties.label', 'Birth Date')
            ->set('elements.0.properties.min', '1900-01-01')
            ->set('elements.0.properties.max', '2020-12-31')
            ->assertSet('elements.0.properties.min', '1900-01-01')
            ->assertSet('elements.0.properties.max', '2020-12-31');
    }

    /** @test */
    public function form_builder_can_configure_number_field_settings()
    {
        $form = Form::factory()->create(['user_id' => $this->user->id]);

        Livewire::actingAs($this->user)
            ->test(FormBuilder::class, ['form' => $form])
            ->call('addElement', 'number')
            ->set('elements.0.properties.label', 'Age')
            ->set('elements.0.properties.min', '18')
            ->set('elements.0.properties.max', '100')
            ->set('elements.0.properties.step', '1')
            ->assertSet('elements.0.properties.min', '18')
            ->assertSet('elements.0.properties.max', '100')
            ->assertSet('elements.0.properties.step', '1');
    }

    /** @test */
    public function form_builder_can_preview_form()
    {
        $form = Form::factory()->create([
            'user_id' => $this->user->id,
            'elements' => [
                [
                    'id' => 'element-1',
                    'type' => 'text',
                    'properties' => ['label' => 'Name'],
                    'styles' => ['desktop' => [], 'tablet' => [], 'mobile' => []],
                ],
            ],
        ]);

        Livewire::actingAs($this->user)
            ->test(FormBuilder::class, ['form' => $form])
            ->call('togglePreview')
            ->assertSet('showPreview', true)
            ->call('togglePreview')
            ->assertSet('showPreview', false);
    }

    /** @test */
    public function form_builder_can_export_form_configuration()
    {
        $form = Form::factory()->create([
            'user_id' => $this->user->id,
            'elements' => [
                [
                    'id' => 'element-1',
                    'type' => 'text',
                    'properties' => ['label' => 'Name'],
                    'styles' => ['desktop' => [], 'tablet' => [], 'mobile' => []],
                ],
            ],
        ]);

        Livewire::actingAs($this->user)
            ->test(FormBuilder::class, ['form' => $form])
            ->call('exportConfiguration')
            ->assertDispatched('download', [
                'filename' => 'form-configuration.json',
                'content' => json_encode($form->toArray()),
            ]);
    }
} 