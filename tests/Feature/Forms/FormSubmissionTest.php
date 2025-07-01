<?php

namespace Tests\Feature\Forms;

use App\Livewire\Frontend\FormDisplay;
use App\Models\Form;
use App\Models\FormSubmission;
use App\Services\FormBuilder\FormSubmissionErrorHandler;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FormSubmissionTest extends TestCase
{
    use RefreshDatabase;

    private FormSubmissionErrorHandler $errorHandler;

    protected function setUp(): void
    {
        parent::setUp();
        $this->errorHandler = new FormSubmissionErrorHandler();
        Storage::fake('public');
    }

    /** @test */
    public function form_can_be_submitted_successfully()
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
                [
                    'id' => 'element-2',
                    'type' => 'email',
                    'properties' => ['label' => 'Email', 'placeholder' => '', 'fluxProps' => ['clearable' => false, 'copyable' => false, 'viewable' => false]],
                    'validation' => ['rules' => ['required', 'email']],
                    'styles' => ['desktop' => [], 'tablet' => [], 'mobile' => []],
                ],
            ],
        ]);

        $formData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ];

        $result = $this->errorHandler->handleSubmission($form, $formData);

        $this->assertTrue($result['success']);
        $this->assertEquals('Form submitted successfully!', $result['message']);
        $this->assertNotNull($result['submission_id']);

        $this->assertDatabaseHas('form_submissions', [
            'form_id' => $form->id,
            'id' => $result['submission_id'],
        ]);
    }

    /** @test */
    public function form_submission_validates_required_fields()
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

        $formData = ['name' => ''];

        $result = $this->errorHandler->handleSubmission($form, $formData);

        $this->assertFalse($result['success']);
        $this->assertEquals('Validation failed', $result['message']);
        $this->assertNotNull($result['errors']);
        $this->assertArrayHasKey('name', $result['errors']);
    }

    /** @test */
    public function form_submission_validates_email_format()
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

        $formData = ['email' => 'invalid-email'];

        $result = $this->errorHandler->handleSubmission($form, $formData);

        $this->assertFalse($result['success']);
        $this->assertNotNull($result['errors']);
        $this->assertArrayHasKey('email', $result['errors']);
    }

    /** @test */
    public function form_submission_handles_file_uploads()
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
                        'placeholder' => '',
                        'fluxProps' => ['clearable' => false, 'copyable' => false, 'viewable' => false]
                    ],
                    'validation' => ['rules' => ['required']],
                    'styles' => ['desktop' => [], 'tablet' => [], 'mobile' => []],
                ],
            ],
        ]);

        $file = UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');
        $formData = ['document' => $file];

        $result = $this->errorHandler->handleSubmission($form, $formData);

        $this->assertTrue($result['success']);
        $this->assertNotNull($result['submission_id']);

        $submission = FormSubmission::find($result['submission_id']);
        $this->assertNotNull($submission);
        $this->assertArrayHasKey('document', $submission->data);
        $this->assertEquals('document.pdf', $submission->data['document']['original_name']);
    }

    /** @test */
    public function form_submission_rejects_invalid_file_types()
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
                        'placeholder' => '',
                        'fluxProps' => ['clearable' => false, 'copyable' => false, 'viewable' => false]
                    ],
                    'validation' => ['rules' => ['required']],
                    'styles' => ['desktop' => [], 'tablet' => [], 'mobile' => []],
                ],
            ],
        ]);

        $file = UploadedFile::fake()->create('script.js', 100, 'application/javascript');
        $formData = ['document' => $file];

        $result = $this->errorHandler->handleSubmission($form, $formData);

        $this->assertFalse($result['success']);
        $this->assertStringContainsString('File type or size not allowed', $result['message']);
    }

    /** @test */
    public function form_submission_rejects_oversized_files()
    {
        $form = Form::factory()->create([
            'elements' => [
                [
                    'id' => 'element-1',
                    'type' => 'file',
                    'properties' => [
                        'label' => 'Document',
                        'accept' => '.pdf,.doc,.docx',
                        'maxSize' => '1KB',
                        'placeholder' => '',
                        'fluxProps' => ['clearable' => false, 'copyable' => false, 'viewable' => false]
                    ],
                    'validation' => ['rules' => ['required']],
                    'styles' => ['desktop' => [], 'tablet' => [], 'mobile' => []],
                ],
            ],
        ]);

        $file = UploadedFile::fake()->create('large-document.pdf', 2000, 'application/pdf');
        $formData = ['document' => $file];

        $result = $this->errorHandler->handleSubmission($form, $formData);

        $this->assertFalse($result['success']);
        $this->assertStringContainsString('File type or size not allowed', $result['message']);
    }

    /** @test */
    public function form_submission_sanitizes_input_data()
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

        $maliciousData = [
            'name' => '<script>alert("xss")</script>John Doe',
        ];

        $result = $this->errorHandler->handleSubmission($form, $maliciousData);

        $this->assertTrue($result['success']);
        
        $submission = FormSubmission::find($result['submission_id']);
        $this->assertNotNull($submission);
        
        // The data should be sanitized before storage
        $this->assertStringNotContainsString('<script>', $submission->data['name']);
    }

    /** @test */
    public function form_submission_enforces_rate_limiting()
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

        $formData = ['name' => 'John Doe'];
        $ipAddress = '192.168.1.1';

        // First submission should succeed
        $result1 = $this->errorHandler->handleSubmission($form, $formData, $ipAddress);
        $this->assertTrue($result1['success']);

        // Second submission from same IP should be rate limited
        $result2 = $this->errorHandler->handleSubmission($form, $formData, $ipAddress);
        $this->assertFalse($result2['success']);
        $this->assertStringContainsString('rate limit', $result2['message']);
    }

    /** @test */
    public function form_submission_handles_disabled_form()
    {
        $form = Form::factory()->create([
            'settings' => ['enabled' => false],
            'elements' => [
                [
                    'id' => 'element-1',
                    'type' => 'text',
                    'properties' => ['label' => 'Name'],
                    'styles' => ['desktop' => [], 'tablet' => [], 'mobile' => []],
                ],
            ],
        ]);

        $formData = ['name' => 'John Doe'];

        $result = $this->errorHandler->handleSubmission($form, $formData);

        $this->assertFalse($result['success']);
        $this->assertEquals('Form is not available or has been disabled.', $result['message']);
    }

    /** @test */
    public function form_submission_handles_form_without_elements()
    {
        $form = Form::factory()->create([
            'elements' => [],
        ]);

        $formData = ['name' => 'John Doe'];

        $result = $this->errorHandler->handleSubmission($form, $formData);

        $this->assertFalse($result['success']);
        $this->assertEquals('Form is not available or has been disabled.', $result['message']);
    }

    /** @test */
    public function form_submission_logs_errors_appropriately()
    {
        $form = Form::factory()->create([
            'elements' => [
                [
                    'id' => 'element-1',
                    'type' => 'text',
                    'properties' => ['label' => 'Name'],
                    'validation' => ['rules' => ['required']],
                    'styles' => ['desktop' => [], 'tablet' => [], 'mobile' => []],
                ],
            ],
        ]);

        $formData = ['name' => ''];

        $result = $this->errorHandler->handleSubmission($form, $formData);

        $this->assertFalse($result['success']);
        $this->assertNotNull($result['errors']);
        
        // Verify that the error is properly formatted
        $this->assertArrayHasKey('name', $result['errors']);
        $this->assertIsString($result['errors']['name']);
    }

    /** @test */
    public function form_submission_handles_multiple_validation_rules()
    {
        $form = Form::factory()->create([
            'elements' => [
                [
                    'id' => 'element-1',
                    'type' => 'text',
                    'properties' => ['label' => 'Username'],
                    'validation' => ['rules' => ['required', 'min:3', 'max:20']],
                    'styles' => ['desktop' => [], 'tablet' => [], 'mobile' => []],
                ],
            ],
        ]);

        $formData = ['username' => 'ab']; // Too short

        $result = $this->errorHandler->handleSubmission($form, $formData);

        $this->assertFalse($result['success']);
        $this->assertNotNull($result['errors']);
        $this->assertArrayHasKey('username', $result['errors']);
    }

    /** @test */
    public function form_submission_handles_numeric_validation()
    {
        $form = Form::factory()->create([
            'elements' => [
                [
                    'id' => 'element-1',
                    'type' => 'number',
                    'properties' => ['label' => 'Age'],
                    'validation' => ['rules' => ['required', 'numeric', 'min_value:18', 'max_value:100']],
                    'styles' => ['desktop' => [], 'tablet' => [], 'mobile' => []],
                ],
            ],
        ]);

        $formData = ['age' => '15']; // Below minimum

        $result = $this->errorHandler->handleSubmission($form, $formData);

        $this->assertFalse($result['success']);
        $this->assertNotNull($result['errors']);
        $this->assertArrayHasKey('age', $result['errors']);
    }
} 