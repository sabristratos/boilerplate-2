<?php

declare(strict_types=1);

use App\Models\Form;
use App\Models\User;

describe('Form Basic Tests', function () {
    it('can create a form', function () {
        $user = User::factory()->create();
        $form = Form::factory()->for($user)->create();

        expect($form)->toBeInstanceOf(Form::class)
            ->and($form->user_id)->toBe($user->id);
    });

    it('can access form relationships', function () {
        $user = User::factory()->create();
        $form = Form::factory()->for($user)->create();

        expect($form->user)->toBeInstanceOf(User::class)
            ->and($form->user->id)->toBe($user->id);
    });

    it('can check draft status', function () {
        $form = Form::factory()->create();

        expect($form->hasDraftChanges())->toBeFalse();

        $form->draft_name = ['en' => 'Draft Name'];
        expect($form->hasDraftChanges())->toBeTrue();
    });
}); 