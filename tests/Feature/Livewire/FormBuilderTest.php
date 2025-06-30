<?php

use App\Livewire\FormBuilder;
use App\Models\Form;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('renders successfully', function () {
    $user = User::factory()->create();
    $form = Form::factory()->create();

    Livewire::actingAs($user)
        ->test(FormBuilder::class, ['form' => $form])
        ->assertStatus(200);
});

it('can add a text element to the form', function () {
    $user = User::factory()->create();
    $form = Form::factory()->create(['user_id' => $user->id]);

    Livewire::actingAs($user)
        ->test(FormBuilder::class, ['form' => $form])
        ->assertSet('elements', [])
        ->call('addElement', 'text')
        ->assertCount('elements', 1)
        ->assertSet('elements.0.type', 'text');
});

it('can reorder elements', function () {
    $user = User::factory()->create();
    $form = Form::factory()->create([
        'user_id' => $user->id,
        'elements' => [
            [
                'id' => 'element-1',
                'type' => 'text',
                'properties' => ['label' => 'First', 'placeholder' => ''],
                'styles' => ['desktop' => [], 'tablet' => [], 'mobile' => []],
            ],
            [
                'id' => 'element-2',
                'type' => 'email',
                'properties' => ['label' => 'Second', 'placeholder' => ''],
                'styles' => ['desktop' => [], 'tablet' => [], 'mobile' => []],
            ],
        ],
    ]);

    Livewire::actingAs($user)
        ->test(FormBuilder::class, ['form' => $form])
        ->assertSet('elements.0.id', 'element-1')
        ->assertSet('elements.1.id', 'element-2')
        ->call('handleReorder', ['element-2', 'element-1'])
        ->assertSet('elements.0.id', 'element-2')
        ->assertSet('elements.1.id', 'element-1');
});

it('can update a property of a selected element', function () {
    $user = User::factory()->create();
    $form = Form::factory()->create([
        'user_id' => $user->id,
        'elements' => [
            [
                'id' => 'element-1',
                'type' => 'text',
                'properties' => ['label' => 'Old Label', 'placeholder' => ''],
                'styles' => ['desktop' => [], 'tablet' => [], 'mobile' => []],
            ],
        ],
    ]);

    Livewire::actingAs($user)
        ->test(FormBuilder::class, ['form' => $form])
        ->set('selectedElementId', 'element-1')
        ->assertSet('elements.0.properties.label', 'Old Label')
        ->set('elements.0.properties.label', 'New Label')
        ->assertSet('elements.0.properties.label', 'New Label');
});

it('can save the form', function () {
    $user = User::factory()->create();
    $form = Form::factory()->create([
        'user_id' => $user->id,
        'settings' => ['backgroundColor' => '#ffffff'],
    ]);

    Livewire::actingAs($user)
        ->test(FormBuilder::class, ['form' => $form])
        ->set('settings.backgroundColor', '#000000')
        ->call('save');

    expect($form->fresh()->settings['backgroundColor'])->toBe('#000000');
});

it('can delete an element', function () {
    $user = User::factory()->create();
    $form = Form::factory()->create([
        'user_id' => $user->id,
        'elements' => [
            [
                'id' => 'element-1',
                'type' => 'text',
                'properties' => ['label' => 'Test Label', 'placeholder' => ''],
                'styles' => ['desktop' => [], 'tablet' => [], 'mobile' => []],
            ],
        ],
    ]);

    Livewire::actingAs($user)
        ->test(FormBuilder::class, ['form' => $form])
        ->assertCount('elements', 1)
        ->call('deleteElement', 'element-1')
        ->assertCount('elements', 0);
});
