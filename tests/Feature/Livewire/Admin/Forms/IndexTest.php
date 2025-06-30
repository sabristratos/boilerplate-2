<?php

namespace Tests\Feature\Livewire\Admin\Forms;

use App\Livewire\Admin\Forms\Index;
use App\Models\Form;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('renders successfully', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(Index::class)
        ->assertStatus(200);
});

it('displays a list of forms for the authenticated user', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    Form::factory()->count(3)->create(['user_id' => $user->id]);
    Form::factory()->count(2)->create(['user_id' => $otherUser->id]);

    Livewire::actingAs($user)
        ->test(Index::class)
        ->assertViewHas('forms', function ($forms) {
            return $forms->count() === 3;
        });
});

it('can create a new form', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(Index::class)
        ->set('newFormName', 'My Awesome Form')
        ->call('create')
        ->assertRedirect(route('admin.forms.edit', Form::first()));

    $this->assertDatabaseCount('forms', 1);
    $form = Form::first();
    expect($form->user_id)->toBe($user->id);
    expect($form->getTranslation('name', 'en'))->toBe('My Awesome Form');
});
