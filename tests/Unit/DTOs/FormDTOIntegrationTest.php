<?php

declare(strict_types=1);

namespace Tests\Unit\DTOs;

use App\DTOs\FormDTO;
use App\DTOs\DTOFactory;
use App\Models\Form;
use App\Models\User;
use App\Services\FormService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FormDTOIntegrationTest extends TestCase
{
    use RefreshDatabase;

    private FormService $formService;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->formService = app(FormService::class);
        $this->user = User::factory()->create();
    }

    /** @test */
    public function it_can_create_form_dto_from_model()
    {
        $form = Form::factory()->create([
            'user_id' => $this->user->id,
            'name' => ['en' => 'Test Form', 'fr' => 'Formulaire Test'],
            'elements' => [
                [
                    'id' => 'field_1',
                    'type' => 'text',
                    'properties' => ['label' => 'Name'],
                    'validation' => ['rules' => ['required']]
                ]
            ],
            'settings' => ['theme' => 'default']
        ]);

        $formDto = DTOFactory::createFormDTO($form);

        $this->assertInstanceOf(FormDTO::class, $formDto);
        $this->assertEquals($form->id, $formDto->id);
        $this->assertEquals($this->user->id, $formDto->userId);
        $this->assertEquals(['en' => 'Test Form', 'fr' => 'Formulaire Test'], $formDto->name);
        $this->assertCount(1, $formDto->elements);
        $this->assertEquals('default', $formDto->getSetting('theme'));
    }

    /** @test */
    public function it_can_create_form_dto_for_creation()
    {
        $formDto = DTOFactory::createFormDTOForCreation(
            ['en' => 'New Form'],
            [
                [
                    'id' => 'field_1',
                    'type' => 'email',
                    'properties' => ['label' => 'Email'],
                    'validation' => ['rules' => ['required', 'email']]
                ]
            ],
            ['theme' => 'modern'],
            $this->user->id
        );

        $this->assertInstanceOf(FormDTO::class, $formDto);
        $this->assertNull($formDto->id);
        $this->assertEquals($this->user->id, $formDto->userId);
        $this->assertEquals(['en' => 'New Form'], $formDto->name);
        $this->assertCount(1, $formDto->elements);
        $this->assertEquals('modern', $formDto->getSetting('theme'));
    }

    /** @test */
    public function it_can_validate_form_dto()
    {
        $formDto = DTOFactory::createFormDTOForCreation(
            [], // Empty name should fail validation
            [
                [
                    // missing 'type'
                    'properties' => ['label' => 'Name']
                ]
            ]
        );

        $this->assertFalse($formDto->isValid());
        $errors = $formDto->validate();
        $this->assertArrayHasKey('name', $errors);
        $this->assertArrayHasKey('elements.0.type', $errors);
    }

    /** @test */
    public function it_can_get_form_name_for_locale()
    {
        $formDto = DTOFactory::createFormDTOForCreation([
            'en' => 'English Name',
            'fr' => 'French Name'
        ]);

        app()->setLocale('en');
        $this->assertEquals('English Name', $formDto->getNameForLocale());
        $this->assertEquals('English Name', $formDto->getNameForLocale('en'));
        $this->assertEquals('French Name', $formDto->getNameForLocale('fr'));
        app()->setLocale('fr');
        $this->assertEquals('French Name', $formDto->getNameForLocale());
    }

    /** @test */
    public function it_can_get_elements_by_type()
    {
        $formDto = DTOFactory::createFormDTOForCreation(
            ['en' => 'Test Form'],
            [
                ['id' => 'field_1', 'type' => 'text', 'properties' => ['label' => 'Name']],
                ['id' => 'field_2', 'type' => 'email', 'properties' => ['label' => 'Email']],
                ['id' => 'field_3', 'type' => 'text', 'properties' => ['label' => 'Phone']]
            ]
        );

        $textElements = $formDto->getElementsByType('text');
        $this->assertCount(2, $textElements);
        
        $emailElements = $formDto->getElementsByType('email');
        $this->assertCount(1, $emailElements);
    }

    /** @test */
    public function it_can_get_required_fields()
    {
        $formDto = DTOFactory::createFormDTOForCreation(
            ['en' => 'Test Form'],
            [
                [
                    'id' => 'field_1',
                    'type' => 'text',
                    'properties' => ['label' => 'Name'],
                    'validation' => ['rules' => ['required']]
                ],
                [
                    'id' => 'field_2',
                    'type' => 'email',
                    'properties' => ['label' => 'Email'],
                    'validation' => ['rules' => ['email']] // Not required
                ]
            ]
        );

        $requiredFields = $formDto->getRequiredFields();
        $this->assertCount(1, $requiredFields);
        $this->assertContains($formDto->getFieldNames()[0], $requiredFields);
    }

    /** @test */
    public function it_can_check_for_file_uploads()
    {
        $formDto = DTOFactory::createFormDTOForCreation(
            ['en' => 'Test Form'],
            [
                ['id' => 'field_1', 'type' => 'text', 'properties' => ['label' => 'Name']],
                ['id' => 'field_2', 'type' => 'file', 'properties' => ['label' => 'Document']]
            ]
        );

        $this->assertTrue($formDto->hasFileUploads());
    }

    /** @test */
    public function it_can_validate_form_data_using_service()
    {
        $formDto = DTOFactory::createFormDTOForCreation(
            ['en' => 'Test Form'],
            [
                [
                    'id' => 'field_1',
                    'type' => 'text',
                    'properties' => ['label' => 'Name'],
                    'validation' => ['rules' => ['required']]
                ]
            ]
        );

        $requiredField = $formDto->getFieldNames()[0];
        // Test with missing required field
        $formData = [];
        $errors = $this->formService->validateFormData($formDto, $formData);
        $this->assertArrayHasKey($requiredField, $errors);

        // Test with valid data
        $formData = [$requiredField => 'John Doe'];
        $errors = $this->formService->validateFormData($formDto, $formData);
        $this->assertEmpty($errors);
    }

    /** @test */
    public function it_can_create_form_using_service_with_dto()
    {
        $formDto = DTOFactory::createFormDTOForCreation(
            ['en' => 'Service Test Form'],
            [
                [
                    'id' => 'field_1',
                    'type' => 'text',
                    'properties' => ['label' => 'Name'],
                    'validation' => ['rules' => ['required']]
                ]
            ],
            ['theme' => 'modern'],
            $this->user->id
        );

        $form = $this->formService->createForm($formDto);

        $this->assertInstanceOf(Form::class, $form);
        $this->assertEquals($this->user->id, $form->user_id);
        $this->assertEquals(['en' => 'Service Test Form'], $form->getTranslations('name'));
        $this->assertCount(1, $form->elements);
        $this->assertEquals('modern', $form->settings['theme']);
    }

    /** @test */
    public function it_can_update_form_using_service_with_dto()
    {
        $form = Form::factory()->create([
            'user_id' => $this->user->id,
            'name' => ['en' => 'Original Name'],
            'elements' => [],
            'settings' => ['theme' => 'default']
        ]);

        $formDto = DTOFactory::createFormDTOForCreation(
            ['en' => 'Updated Name'],
            [
                [
                    'id' => 'field_1',
                    'type' => 'text',
                    'properties' => ['label' => 'Name'],
                    'validation' => ['rules' => ['required']]
                ]
            ],
            ['theme' => 'modern'],
            $this->user->id
        );

        $updatedForm = $this->formService->updateForm($form, $formDto);

        $this->assertEquals(['en' => 'Updated Name'], $updatedForm->getTranslations('name'));
        $this->assertCount(1, $updatedForm->elements);
        $this->assertEquals('modern', $updatedForm->settings['theme']);
    }

    /** @test */
    public function it_throws_exception_for_invalid_dto_in_service()
    {
        $this->expectException(\InvalidArgumentException::class);

        $invalidFormDto = DTOFactory::createFormDTOForCreation(
            [], // Empty name should fail validation
            []
        );

        $form = Form::factory()->create(['user_id' => $this->user->id]);
        $this->formService->updateForm($form, $invalidFormDto);
    }
} 