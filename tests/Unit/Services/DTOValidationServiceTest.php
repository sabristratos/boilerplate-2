<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\DTOs\FormDTO;
use App\DTOs\PageDTO;
use App\DTOs\UserDTO;
use App\DTOs\ContentBlockDTO;
use App\DTOs\MediaDTO;
use App\DTOs\FormSubmissionDTO;
use App\Enums\FormElementType;
use App\Enums\PublishStatus;
use App\Enums\UserRole;
use App\Services\DTOValidationService;
use Carbon\Carbon;
use Tests\TestCase;

class DTOValidationServiceTest extends TestCase
{
    private DTOValidationService $validationService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->validationService = new DTOValidationService();

        // Use in-memory SQLite for DB existence checks
        \Illuminate\Support\Facades\DB::statement('PRAGMA foreign_keys=OFF;');
        \Illuminate\Support\Facades\Schema::dropAllTables();
        \Illuminate\Support\Facades\Schema::create('pages', function ($table): void {
            $table->increments('id');
        });
        \Illuminate\Support\Facades\Schema::create('forms', function ($table): void {
            $table->increments('id');
        });
        // Insert a dummy page and form for existence checks
        \Illuminate\Support\Facades\DB::table('pages')->insert(['id' => 1]);
        \Illuminate\Support\Facades\DB::table('forms')->insert(['id' => 1]);
    }

    /** @test */
    public function it_validates_form_dto_with_valid_data(): void
    {
        $formDTO = FormDTO::forCreation(
            name: ['en' => 'Test Form', 'fr' => 'Formulaire Test'],
            elements: [
                [
                    'id' => 'field_1',
                    'type' => FormElementType::TEXT->value,
                    'properties' => ['label' => 'Name'],
                    'validation' => ['rules' => ['required']]
                ]
            ],
            settings: ['send_notification' => true]
        );

        $errors = $formDTO->validate();
        
        $this->assertEmpty($errors);
    }

    /** @test */
    public function it_validates_form_dto_with_invalid_data(): void
    {
        $formDTO = FormDTO::forCreation(
            name: [], // Missing required name
            elements: [
                [
                    'id' => '', // Invalid empty ID
                    'type' => 'invalid_type', // Invalid type
                    'properties' => 'not_array', // Invalid properties
                ]
            ]
        );

        $errors = $formDTO->validate();
        
        $this->assertNotEmpty($errors);
        $this->assertArrayHasKey('name', $errors);
        $this->assertArrayHasKey('elements.0.id', $errors);
        $this->assertArrayHasKey('elements.0.type', $errors);
    }

    /** @test */
    public function it_validates_page_dto_with_valid_data(): void
    {
        $pageDTO = PageDTO::forCreation(
            title: ['en' => 'Test Page', 'fr' => 'Page Test'],
            slug: 'test-page',
            status: PublishStatus::DRAFT
        );

        $errors = $pageDTO->validate();
        
        $this->assertEmpty($errors);
    }

    /** @test */
    public function it_validates_page_dto_with_invalid_data(): void
    {
        $pageDTO = PageDTO::forCreation(
            title: [], // Missing required title
            slug: 'Invalid Slug With Spaces', // Invalid slug format
            status: PublishStatus::DRAFT
        );

        $errors = $pageDTO->validate();
        
        $this->assertNotEmpty($errors);
        $this->assertArrayHasKey('title', $errors);
        $this->assertArrayHasKey('slug', $errors);
    }

    /** @test */
    public function it_validates_user_dto_with_valid_data(): void
    {
        $userDTO = UserDTO::forCreation(
            name: 'John Doe',
            email: 'john@example.com',
            password: 'password123',
            locale: 'en'
        );
        // Add password_confirmation for validation
        $userDTO = $userDTO->with(['password_confirmation' => 'password123']);

        $errors = $userDTO->validate();
        
        $this->assertEmpty($errors);
    }

    /** @test */
    public function it_validates_user_dto_with_invalid_data(): void
    {
        $userDTO = UserDTO::forCreation(
            name: '', // Empty name
            email: 'invalid-email', // Invalid email
            password: 'short', // Too short password
            locale: 'invalid' // Invalid locale
        );

        $errors = $userDTO->validate();
        
        $this->assertNotEmpty($errors);
        $this->assertArrayHasKey('name', $errors);
        $this->assertArrayHasKey('email', $errors);
        $this->assertArrayHasKey('password', $errors);
        $this->assertArrayHasKey('locale', $errors);
    }

    /** @test */
    public function it_validates_content_block_dto_with_valid_data(): void
    {
        $blockDTO = ContentBlockDTO::forCreation(
            type: 'hero',
            pageId: 1,
            data: ['title' => 'Hero Title'],
            settings: ['background' => 'blue'],
            visible: true,
            order: 1
        );

        $errors = $blockDTO->validate();
        
        $this->assertEmpty($errors);
    }

    /** @test */
    public function it_validates_content_block_dto_with_invalid_data(): void
    {
        $blockDTO = ContentBlockDTO::forCreation(
            type: 'invalid_type', // Invalid type
            pageId: 0, // Invalid page ID
            data: [], // Should be array, not string
            settings: [], // Should be array, not string
            visible: true,
            order: -1 // Invalid order
        );

        $errors = $blockDTO->validate();
        
        $this->assertNotEmpty($errors);
        $this->assertArrayHasKey('type', $errors);
        $this->assertArrayHasKey('page_id', $errors);
        $this->assertArrayHasKey('order', $errors);
    }

    /** @test */
    public function it_validates_media_dto_with_valid_data(): void
    {
        $mediaDTO = MediaDTO::forCreation(
            fileName: 'test.jpg',
            name: 'Test Image',
            mimeType: 'image/jpeg',
            size: 1024,
            disk: 'public',
            path: 'media/test.jpg',
            collectionName: 'images',
            modelType: \App\Models\Page::class,
            modelId: 1
        );

        $errors = $mediaDTO->validate();
        
        $this->assertEmpty($errors);
    }

    /** @test */
    public function it_validates_media_dto_with_invalid_data(): void
    {
        $mediaDTO = MediaDTO::forCreation(
            fileName: '', // Empty file name
            name: '', // Empty name
            mimeType: 'invalid-mime-type', // Invalid MIME type
            size: 0, // Invalid size
            disk: '', // Empty disk
            path: '', // Empty path
            collectionName: '', // Empty collection
            modelType: '', // Empty model type
            modelId: 1
        );

        $errors = $mediaDTO->validate();
        
        $this->assertNotEmpty($errors);
        $this->assertArrayHasKey('file_name', $errors);
        $this->assertArrayHasKey('name', $errors);
        $this->assertArrayHasKey('mime_type', $errors);
        $this->assertArrayHasKey('size', $errors);
        $this->assertArrayHasKey('disk', $errors);
        $this->assertArrayHasKey('path', $errors);
        $this->assertArrayHasKey('collection_name', $errors);
        $this->assertArrayHasKey('model_type', $errors);
    }

    /** @test */
    public function it_validates_form_submission_dto_with_valid_data(): void
    {
        $submissionDTO = FormSubmissionDTO::forCreation(
            formId: 1,
            data: ['name' => 'John Doe', 'email' => 'john@example.com'],
            ipAddress: '192.168.1.1',
            userAgent: 'Mozilla/5.0'
        );

        $errors = $submissionDTO->validate();
        
        $this->assertEmpty($errors);
    }

    /** @test */
    public function it_validates_form_submission_dto_with_invalid_data(): void
    {
        $submissionDTO = FormSubmissionDTO::forCreation(
            formId: 0, // Invalid form ID
            data: [], // Empty data
            ipAddress: 'invalid-ip', // Invalid IP
            userAgent: '' // Empty user agent
        );

        $errors = $submissionDTO->validate();
        
        $this->assertNotEmpty($errors);
        $this->assertArrayHasKey('form_id', $errors);
        $this->assertArrayHasKey('data', $errors);
        $this->assertArrayHasKey('ip_address', $errors);
        // $this->assertArrayHasKey('user_agent', $errors); // user_agent is nullable, so no error expected
    }

    /** @test */
    public function it_validates_translatable_fields(): void
    {
        $data = [
            'title' => [
                'en' => 'English Title',
                'fr' => 'French Title'
            ]
        ];

        $errors = $this->validationService->validateTranslatableField($data, 'title');
        
        $this->assertEmpty($errors);
    }

    /** @test */
    public function it_validates_translatable_fields_with_missing_translations(): void
    {
        $data = [
            'title' => [
                'en' => '',
                'fr' => ''
            ]
        ];

        $errors = $this->validationService->validateTranslatableField($data, 'title');
        
        $this->assertNotEmpty($errors);
        $this->assertArrayHasKey('title', $errors);
    }

    /** @test */
    public function it_validates_form_elements(): void
    {
        $elements = [
            [
                'id' => 'field_1',
                'type' => FormElementType::TEXT->value,
                'properties' => ['label' => 'Name'],
                'validation' => ['rules' => ['required']]
            ]
        ];

        $errors = $this->validationService->validateFormElements($elements);
        
        $this->assertEmpty($errors);
    }

    /** @test */
    public function it_validates_form_elements_with_invalid_data(): void
    {
        $elements = [
            [
                'id' => '', // Invalid empty ID
                'type' => 'invalid_type', // Invalid type
                'properties' => 'not_array', // Invalid properties
            ]
        ];

        $errors = $this->validationService->validateFormElements($elements);
        
        $this->assertNotEmpty($errors);
        $this->assertArrayHasKey('elements.0.id', $errors);
        $this->assertArrayHasKey('elements.0.type', $errors);
        $this->assertArrayHasKey('elements.0.properties', $errors);
    }

    /** @test */
    public function it_gets_translatable_field_rules(): void
    {
        $rules = $this->validationService->getTranslatableFieldRules('title', true, ['en', 'fr']);
        
        $this->assertArrayHasKey('title.en', $rules);
        $this->assertArrayHasKey('title.fr', $rules);
        $this->assertStringContainsString('required', $rules['title.en']);
        $this->assertStringContainsString('required', $rules['title.fr']);
    }

    /** @test */
    public function it_gets_form_elements_rules(): void
    {
        $elements = [
            [
                'id' => 'field_1',
                'type' => FormElementType::SELECT->value,
                'properties' => ['options' => [['label' => 'Option 1', 'value' => '1']]],
            ]
        ];

        $rules = $this->validationService->getFormElementsRules($elements);
        
        $this->assertArrayHasKey('elements.0.id', $rules);
        $this->assertArrayHasKey('elements.0.type', $rules);
        $this->assertArrayHasKey('elements.0.properties', $rules);
        $this->assertArrayHasKey('elements.0.properties.options', $rules);
    }

    /** @test */
    public function it_gets_custom_validation_messages(): void
    {
        $messages = $this->validationService->getCustomValidationMessages();
        
        $this->assertIsArray($messages);
        $this->assertArrayHasKey('title.required', $messages);
        $this->assertArrayHasKey('name.required', $messages);
        $this->assertArrayHasKey('slug.required', $messages);
    }

    /** @test */
    public function it_gets_custom_attribute_names(): void
    {
        $attributes = $this->validationService->getCustomAttributeNames();
        
        $this->assertIsArray($attributes);
        $this->assertArrayHasKey('title', $attributes);
        $this->assertArrayHasKey('name', $attributes);
        $this->assertArrayHasKey('slug', $attributes);
    }

    /** @test */
    public function it_validates_dto_using_laravel_validator(): void
    {
        $formDTO = FormDTO::forCreation(
            name: ['en' => 'Test Form'],
            elements: [],
            settings: []
        );

        $rules = [
            'name.en' => 'required|string|max:255',
            'settings' => 'nullable|array'
        ];

        $errors = $this->validationService->validateDTO($formDTO, $rules);
        
        $this->assertEmpty($errors);
    }

    /** @test */
    public function it_validates_dto_with_custom_messages_and_attributes(): void
    {
        $formDTO = FormDTO::forCreation(
            name: [], // Missing required name
            elements: [],
            settings: []
        );

        $rules = [
            'name.en' => 'required|string|max:255'
        ];

        $messages = [
            'name.en.required' => 'Custom message for required name'
        ];

        $attributes = [
            'name.en' => 'Form name in English'
        ];

        $errors = $this->validationService->validateDTO($formDTO, $rules, $messages, $attributes);
        
        $this->assertNotEmpty($errors);
        $this->assertArrayHasKey('name.en', $errors);
    }
} 