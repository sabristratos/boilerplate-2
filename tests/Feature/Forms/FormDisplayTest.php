<?php

namespace Tests\Feature\Forms;

use App\Livewire\Frontend\FormDisplay;
use App\Models\Form;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class FormDisplayTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /** @test */
    public function form_display_renders_successfully()
    {
        $form = Form::factory()->create([
            'elements' => [
                [
                    'id' => 'element-1',
                    'type' => 'text',
                    'properties' => [
                        'label' => 'Name', 
                        'placeholder' => 'Enter your name',
                        'fluxProps' => ['clearable' => false, 'copyable' => false, 'viewable' => false],
                    ],
                    'styles' => ['desktop' => [], 'tablet' => [], 'mobile' => []],
                ],
                [
                    'id' => 'element-2',
                    'type' => 'email',
                    'properties' => [
                        'label' => 'Email', 
                        'placeholder' => 'Enter your email',
                        'fluxProps' => ['clearable' => false, 'copyable' => false, 'viewable' => false],
                    ],
                    'styles' => ['desktop' => [], 'tablet' => [], 'mobile' => []],
                ],
            ],
        ]);

        Livewire::test(FormDisplay::class, ['form' => $form])
            ->assertStatus(200)
            ->assertSee('Name')
            ->assertSee('Email');
    }

    /** @test */
    public function form_display_initializes_form_data()
    {
        $form = Form::factory()->create([
            'elements' => [
                [
                    'id' => 'element-1',
                    'type' => 'text',
                    'properties' => [
                        'label' => 'Name',
                        'placeholder' => '',
                        'fluxProps' => ['clearable' => false, 'copyable' => false, 'viewable' => false],
                    ],
                    'styles' => ['desktop' => [], 'tablet' => [], 'mobile' => []],
                ],
                [
                    'id' => 'element-2',
                    'type' => 'email',
                    'properties' => [
                        'label' => 'Email',
                        'placeholder' => '',
                        'fluxProps' => ['clearable' => false, 'copyable' => false, 'viewable' => false],
                    ],
                    'styles' => ['desktop' => [], 'tablet' => [], 'mobile' => []],
                ],
            ],
        ]);

        Livewire::test(FormDisplay::class, ['form' => $form])
            ->assertSet('formData.name', '')
            ->assertSet('formData.email', '');
    }

    /** @test */
    public function form_display_handles_user_input()
    {
        $form = Form::factory()->create([
            'elements' => [
                [
                    'id' => 'element-1',
                    'type' => 'text',
                    'properties' => [
                        'label' => 'Name',
                        'placeholder' => '',
                        'fluxProps' => ['clearable' => false, 'copyable' => false, 'viewable' => false],
                    ],
                    'styles' => ['desktop' => [], 'tablet' => [], 'mobile' => []],
                ],
            ],
        ]);

        Livewire::test(FormDisplay::class, ['form' => $form])
            ->set('formData.name', 'John Doe')
            ->assertSet('formData.name', 'John Doe');
    }

    /** @test */
    public function form_display_submits_data_successfully()
    {
        $form = Form::factory()->create([
            'elements' => [
                [
                    'id' => 'element-1',
                    'type' => 'text',
                    'properties' => ['label' => 'Name', 'placeholder' => '', 'fluxProps' => ['clearable' => false, 'copyable' => false, 'viewable' => false]],
                    'validation' => ['rules' => ['required']],
                    'styles' => ['desktop' => [], 'tablet' => [], 'mobile' => []],
                ],
            ],
        ]);

        Livewire::test(FormDisplay::class, ['form' => $form])
            ->set('formData.name', 'John Doe')
            ->call('submit')
            ->assertSet('submitted', true)
            ->assertSet('successMessage', 'Form submitted successfully!');
    }

    /** @test */
    public function form_display_shows_validation_errors()
    {
        $form = Form::factory()->create([
            'elements' => [
                [
                    'id' => 'element-1',
                    'type' => 'text',
                    'properties' => ['label' => 'Name', 'placeholder' => '', 'fluxProps' => ['clearable' => false, 'copyable' => false, 'viewable' => false]],
                    'validation' => ['rules' => ['required']],
                    'styles' => ['desktop' => [], 'tablet' => [], 'mobile' => []],
                ],
            ],
        ]);

        Livewire::test(FormDisplay::class, ['form' => $form])
            ->set('formData.name', '')
            ->call('submit')
            ->assertHasErrors(['formData.name']);
    }

    /** @test */
    public function form_display_handles_email_validation()
    {
        $form = Form::factory()->create([
            'elements' => [
                [
                    'id' => 'element-1',
                    'type' => 'email',
                    'properties' => ['label' => 'Email', 'placeholder' => '', 'fluxProps' => ['clearable' => false, 'copyable' => false, 'viewable' => false]],
                    'validation' => ['rules' => ['required', 'email']],
                    'styles' => ['desktop' => [], 'tablet' => [], 'mobile' => []],
                ],
            ],
        ]);

        Livewire::test(FormDisplay::class, ['form' => $form])
            ->set('formData.email', 'invalid-email')
            ->call('submit')
            ->assertHasErrors(['formData.email']);
    }

    /** @test */
    public function form_display_handles_textarea_fields()
    {
        $form = Form::factory()->create([
            'elements' => [
                [
                    'id' => 'element-1',
                    'type' => 'textarea',
                    'properties' => ['label' => 'Message', 'placeholder' => 'Enter your message', 'fluxProps' => ['clearable' => false, 'copyable' => false, 'viewable' => false]],
                    'styles' => ['desktop' => [], 'tablet' => [], 'mobile' => []],
                ],
            ],
        ]);

        Livewire::test(FormDisplay::class, ['form' => $form])
            ->assertSee('Message')
            ->set('formData.message', 'This is a test message')
            ->assertSet('formData.message', 'This is a test message');
    }

    /** @test */
    public function form_display_handles_select_fields()
    {
        $form = Form::factory()->create([
            'elements' => [
                [
                    'id' => 'element-1',
                    'type' => 'select',
                    'properties' => [
                        'label' => 'Country',
                        'options' => [
                            ['value' => 'us', 'label' => 'United States'],
                            ['value' => 'ca', 'label' => 'Canada'],
                            ['value' => 'uk', 'label' => 'United Kingdom'],
                        ],
                        'fluxProps' => ['clearable' => false, 'copyable' => false, 'viewable' => false],
                    ],
                    'styles' => ['desktop' => [], 'tablet' => [], 'mobile' => []],
                ],
            ],
        ]);

        Livewire::test(FormDisplay::class, ['form' => $form])
            ->assertSee('Country')
            ->assertSee('United States')
            ->assertSee('Canada')
            ->assertSee('United Kingdom')
            ->set('formData.country', 'us')
            ->assertSet('formData.country', 'us');
    }

    /** @test */
    public function form_display_handles_checkbox_fields()
    {
        $form = Form::factory()->create([
            'elements' => [
                [
                    'id' => 'element-1',
                    'type' => 'checkbox',
                    'properties' => [
                        'label' => 'Subscribe to newsletter',
                        'options' => [
                            ['value' => 'yes', 'label' => 'Yes'],
                        ],
                        'fluxProps' => ['clearable' => false, 'copyable' => false, 'viewable' => false],
                    ],
                    'styles' => ['desktop' => [], 'tablet' => [], 'mobile' => []],
                ],
            ],
        ]);

        Livewire::test(FormDisplay::class, ['form' => $form])
            ->assertSee('Subscribe to newsletter')
            ->set('formData.subscribe_to_newsletter', ['yes'])
            ->assertSet('formData.subscribe_to_newsletter', ['yes']);
    }

    /** @test */
    public function form_display_handles_radio_fields()
    {
        $form = Form::factory()->create([
            'elements' => [
                [
                    'id' => 'element-1',
                    'type' => 'radio',
                    'properties' => [
                        'label' => 'Gender',
                        'options' => [
                            ['value' => 'male', 'label' => 'Male'],
                            ['value' => 'female', 'label' => 'Female'],
                            ['value' => 'other', 'label' => 'Other'],
                        ],
                        'fluxProps' => ['clearable' => false, 'copyable' => false, 'viewable' => false],
                    ],
                    'styles' => ['desktop' => [], 'tablet' => [], 'mobile' => []],
                ],
            ],
        ]);

        Livewire::test(FormDisplay::class, ['form' => $form])
            ->assertSee('Gender')
            ->assertSee('Male')
            ->assertSee('Female')
            ->assertSee('Other')
            ->set('formData.gender', 'male')
            ->assertSet('formData.gender', 'male');
    }

    /** @test */
    public function form_display_handles_date_fields()
    {
        $form = Form::factory()->create([
            'elements' => [
                [
                    'id' => 'element-1',
                    'type' => 'date',
                    'properties' => ['label' => 'Birth Date', 'placeholder' => '', 'fluxProps' => ['clearable' => false, 'copyable' => false, 'viewable' => false]],
                    'styles' => ['desktop' => [], 'tablet' => [], 'mobile' => []],
                ],
            ],
        ]);

        Livewire::test(FormDisplay::class, ['form' => $form])
            ->assertSee('Birth Date')
            ->set('formData.birth_date', '1990-01-01')
            ->assertSet('formData.birth_date', '1990-01-01');
    }

    /** @test */
    public function form_display_handles_number_fields()
    {
        $form = Form::factory()->create([
            'elements' => [
                [
                    'id' => 'element-1',
                    'type' => 'number',
                    'properties' => ['label' => 'Age', 'placeholder' => '', 'fluxProps' => ['clearable' => false, 'copyable' => false, 'viewable' => false]],
                    'validation' => ['rules' => ['numeric', 'min_value:18']],
                    'styles' => ['desktop' => [], 'tablet' => [], 'mobile' => []],
                ],
            ],
        ]);

        Livewire::test(FormDisplay::class, ['form' => $form])
            ->assertSee('Age')
            ->set('formData.age', '25')
            ->assertSet('formData.age', '25');
    }

    /** @test */
    public function form_display_handles_file_upload_fields()
    {
        $form = Form::factory()->create([
            'elements' => [
                [
                    'id' => 'element-1',
                    'type' => 'file',
                    'properties' => [
                        'label' => 'Document',
                        'accept' => '.pdf,.doc,.docx',
                        'maxSize' => '2MB',
                        'fluxProps' => ['clearable' => false, 'copyable' => false, 'viewable' => false],
                    ],
                    'styles' => ['desktop' => [], 'tablet' => [], 'mobile' => []],
                ],
            ],
        ]);

        Livewire::test(FormDisplay::class, ['form' => $form])
            ->assertSee('Document')
            ->assertSee('Choose file');
    }

    /** @test */
    public function form_display_generates_consistent_field_names()
    {
        $form = Form::factory()->create([
            'elements' => [
                [
                    'id' => 'element-1',
                    'type' => 'text',
                    'properties' => ['label' => 'Full Name', 'placeholder' => '', 'fluxProps' => ['clearable' => false, 'copyable' => false, 'viewable' => false]],
                    'styles' => ['desktop' => [], 'tablet' => [], 'mobile' => []],
                ],
                [
                    'id' => 'element-2',
                    'type' => 'email',
                    'properties' => ['label' => 'Email Address', 'placeholder' => '', 'fluxProps' => ['clearable' => false, 'copyable' => false, 'viewable' => false]],
                    'styles' => ['desktop' => [], 'tablet' => [], 'mobile' => []],
                ],
            ],
        ]);

        $component = Livewire::test(FormDisplay::class, ['form' => $form]);
        
        // Field names should be generated consistently
        $this->assertArrayHasKey('full_name', $component->get('formData'));
        $this->assertArrayHasKey('email_address', $component->get('formData'));
    }

    /** @test */
    public function form_display_handles_form_without_elements()
    {
        $form = Form::factory()->create([
            'elements' => [],
        ]);

        Livewire::test(FormDisplay::class, ['form' => $form])
            ->assertSet('formData', []);
    }

    /** @test */
    public function form_display_handles_nonexistent_form()
    {
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);
        
        Livewire::test(FormDisplay::class, ['form' => 99999]);
    }
} 