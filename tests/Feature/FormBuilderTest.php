<?php

namespace Tests\Feature;

use App\Enums\FormElementType;
use App\Livewire\FormBuilder;
use App\Models\Form;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class FormBuilderTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    public function test_can_mount_with_existing_form()
    {
        $form = Form::factory()->create([
            'user_id' => $this->user->id,
            'name' => 'Test Form',
            'elements' => [
                [
                    'id' => 'test-element-1',
                    'type' => FormElementType::TEXT->value,
                    'label' => 'Test Field',
                    'order' => 0,
                    'validation' => [
                        'rules' => ['required'],
                        'messages' => [],
                        'values' => [],
                    ],
                    'properties' => [
                        'placeholder' => 'Enter text',
                    ],
                ],
            ],
        ]);

        Livewire::test(FormBuilder::class, ['form' => $form])
            ->assertSet('form.id', $form->id)
            ->assertSet('elements.0.id', 'test-element-1')
            ->assertSet('elements.0.type', FormElementType::TEXT->value);
    }

    public function test_can_mount_with_empty_form()
    {
        $form = Form::factory()->create([
            'user_id' => $this->user->id,
            'elements' => null,
        ]);

        Livewire::test(FormBuilder::class, ['form' => $form])
            ->assertSet('form.id', $form->id)
            ->assertSet('elements', [])
            ->assertSet('draftElements', []);
    }

    public function test_ensures_validation_structure_on_mount()
    {
        $form = Form::factory()->create([
            'user_id' => $this->user->id,
            'elements' => [
                [
                    'id' => 'test-element-1',
                    'type' => FormElementType::TEXT->value,
                    'label' => 'Test Field',
                    'order' => 0,
                    // Missing validation structure
                ],
            ],
        ]);

        Livewire::test(FormBuilder::class, ['form' => $form])
            ->assertSet('elements.0.validation.rules', [])
            ->assertSet('elements.0.validation.messages', [])
            ->assertSet('elements.0.validation.values', []);
    }

    public function test_ensures_properties_structure_on_mount()
    {
        $form = Form::factory()->create([
            'user_id' => $this->user->id,
            'elements' => [
                [
                    'id' => 'test-element-1',
                    'type' => FormElementType::TEXT->value,
                    'label' => 'Test Field',
                    'order' => 0,
                    'validation' => [
                        'rules' => [],
                        'messages' => [],
                        'values' => [],
                    ],
                    // Missing properties structure
                ],
            ],
        ]);

        Livewire::test(FormBuilder::class, ['form' => $form])
            ->assertSet('elements.0.properties', function ($properties) {
                return is_array($properties) && isset($properties['label']);
            });
    }

    // ...
    // Repeat this conversion for all other tests in the Pest file, using PHPUnit methods
    // ...
}
