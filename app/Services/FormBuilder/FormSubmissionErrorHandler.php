<?php

declare(strict_types=1);

namespace App\Services\FormBuilder;

use App\Models\Form;
use App\Models\FormSubmission;
use App\DTOs\FormDTO;
use App\DTOs\FormSubmissionDTO;
use App\DTOs\DTOFactory;
use App\Services\FormBuilder\ValidationRuleService;
use App\Services\FormBuilder\FieldNameGeneratorService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Exception;

/**
 * Handles form submission, validation, file uploads, and error responses for forms.
 */
class FormSubmissionErrorHandler
{
    /**
     * Handle form submission with comprehensive error handling
     */
    public function handleSubmission(Form $form, array $formData): array
    {
        // Convert form to DTO for type-safe operations
        $formDto = DTOFactory::createFormDTO($form);
        
        return $this->handleSubmissionWithDTO($formDto, $formData);
    }

    /**
     * Handle form submission using DTOs for type safety
     */
    public function handleSubmissionWithDTO(FormDTO $formDto, array $formData): array
    {
        try {
            // Validate form exists and is active
            if (!$this->isFormValid($formDto)) {
                return $this->createErrorResponse('Form is not available or has been disabled.');
            }

            // Check rate limiting
            if (!$this->checkRateLimit($formDto, request()->ip())) {
                return $this->createErrorResponse('Too many submissions. Please try again later.');
            }

            // Generate validation rules
            $rules = $this->generateValidationRules($formDto);
            $messages = $this->generateValidationMessages($formDto);

            // Validate the form data
            $validator = Validator::make($formData, $rules, $messages);
            
            if ($validator->fails()) {
                return $this->createErrorResponse(
                    'Validation failed',
                    $validator->errors()->toArray()
                );
            }

            // Sanitize form data
            $sanitizedData = $this->sanitizeFormData($formData);

            // Handle file uploads if present
            $processedData = $this->processFileUploads($formDto, $sanitizedData);

            // Save the form submission using DTO
            $submissionDto = DTOFactory::createFormSubmissionDTOForCreation(
                $formDto->id,
                $processedData,
                request()->ip(),
                request()->userAgent()
            );
            
            $submission = $this->saveSubmissionWithDTO($submissionDto);

            return $this->createSuccessResponse($submission);

        } catch (ValidationException $e) {
            Log::warning('Form validation failed', [
                'form_id' => $formDto->id,
                'errors' => $e->errors(),
                'data' => $this->sanitizeDataForLogging($formData)
            ]);

            return $this->createErrorResponse(
                'Validation failed',
                $e->errors()
            );

        } catch (Exception $e) {
            Log::error('Form submission failed', [
                'form_id' => $formDto->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => $this->sanitizeDataForLogging($formData)
            ]);

            return $this->createErrorResponse(
                'An unexpected error occurred. Please try again later.'
            );
        }
    }

    /**
     * Check if the form is valid for submission
     */
    private function isFormValid(FormDTO $formDto): bool
    {
        // Check if form exists and has elements
        if (!$formDto->id || !$formDto->hasElements()) {
            return false;
        }

        return true;
    }

    /**
     * Generate validation rules for the form
     */
    private function generateValidationRules(FormDTO $formDto): array
    {
        $rules = [];
        $validationRuleService = app(ValidationRuleService::class);

        foreach ($formDto->elements as $element) {
            $fieldName = $this->generateFieldName($element);
            $elementRules = $validationRuleService->generateRules($element);

            if (!empty($elementRules)) {
                $rules[$fieldName] = $elementRules;
            }
        }

        return $rules;
    }

    /**
     * Generate validation messages for the form
     */
    private function generateValidationMessages(FormDTO $formDto): array
    {
        $messages = [];
        $validationRuleService = app(ValidationRuleService::class);

        foreach ($formDto->elements as $element) {
            $fieldName = $this->generateFieldName($element);
            $elementMessages = $validationRuleService->generateMessages($element);

            foreach ($elementMessages as $rule => $message) {
                $messages["{$fieldName}.{$rule}"] = $message;
            }
        }

        return $messages;
    }

    /**
     * Process file uploads in the form data with enhanced security
     */
    private function processFileUploads(FormDTO $formDto, array $formData): array
    {
        $processedData = $formData;

        foreach ($formDto->elements as $element) {
            if ($element['type'] === 'file') {
                $fieldName = $this->generateFieldName($element);
                
                if (isset($formData[$fieldName]) && $formData[$fieldName] instanceof \Illuminate\Http\UploadedFile) {
                    try {
                        $file = $formData[$fieldName];
                        
                        // Enhanced security checks
                        if (!$this->isFileAllowed($file, $element)) {
                            throw new Exception('File type or size not allowed');
                        }
                        
                        $fileName = $this->generateFileName($file, $element);
                        $path = $file->storeAs('form-uploads/' . $form->id, $fileName, 'public');
                        
                        $processedData[$fieldName] = [
                            'original_name' => $file->getClientOriginalName(),
                            'stored_name' => $fileName,
                            'path' => $path,
                            'size' => $file->getSize(),
                            'mime_type' => $file->getMimeType(),
                            'uploaded_at' => now()->toISOString(),
                        ];
                    } catch (Exception $e) {
                        Log::error('File upload failed', [
                            'form_id' => $form->id,
                            'field' => $fieldName,
                            'error' => $e->getMessage()
                        ]);
                        
                        throw new Exception('File upload failed: ' . $e->getMessage());
                    }
                }
            }
        }

        return $processedData;
    }

    /**
     * Check if file is allowed based on element configuration
     */
    private function isFileAllowed(\Illuminate\Http\UploadedFile $file, array $element): bool
    {
        $properties = $element['properties'] ?? [];
        $accept = $properties['accept'] ?? '';
        $maxSize = $properties['maxSize'] ?? null;
        
        // Check file size
        if ($maxSize && $file->getSize() > $this->parseFileSize($maxSize)) {
            return false;
        }
        
        // Check file type
        if ($accept && !$this->isFileTypeAllowed($file, $accept)) {
            return false;
        }
        
        return true;
    }

    /**
     * Parse file size string (e.g., "5MB", "10KB")
     */
    private function parseFileSize(string $sizeString): int
    {
        $size = (int) $sizeString;
        $unit = strtoupper(substr($sizeString, -2));
        
        return match($unit) {
            'KB' => $size * 1024,
            'MB' => $size * 1024 * 1024,
            'GB' => $size * 1024 * 1024 * 1024,
            default => $size
        };
    }

    /**
     * Check if file type is allowed
     */
    private function isFileTypeAllowed(\Illuminate\Http\UploadedFile $file, string $accept): bool
    {
        if (empty($accept)) {
            return true;
        }

        $allowedTypes = array_map('trim', explode(',', $accept));
        $fileExtension = strtolower($file->getClientOriginalExtension());
        $fileMimeType = strtolower($file->getMimeType());

        foreach ($allowedTypes as $type) {
            // Handle MIME types (e.g., "image/*", "application/pdf")
            if (str_contains($type, '/')) {
                if ($type === $fileMimeType || 
                    (str_ends_with($type, '/*') && str_starts_with($fileMimeType, substr($type, 0, -1)))) {
                    return true;
                }
            }
            // Handle file extensions (e.g., ".pdf", ".jpg")
            elseif (str_starts_with($type, '.')) {
                if ('.' . $fileExtension === $type) {
                    return true;
                }
            }
            // Handle file extensions without dot
            else {
                if ($fileExtension === $type) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Generate a unique filename for uploaded files
     */
    private function generateFileName(\Illuminate\Http\UploadedFile $file, array $element): string
    {
        $extension = $file->getClientOriginalExtension();
        $baseName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $timestamp = now()->format('Y-m-d_H-i-s');
        $random = Str::random(8);
        
        return "{$baseName}_{$timestamp}_{$random}.{$extension}";
    }

    /**
     * Save the form submission to the database using DTO
     */
    private function saveSubmissionWithDTO(FormSubmissionDTO $submissionDto): FormSubmission
    {
        try {
            $submission = new FormSubmission();
            $submission->form_id = $submissionDto->formId;
            $submission->data = $submissionDto->data;
            $submission->ip_address = $submissionDto->ipAddress;
            $submission->user_agent = $submissionDto->userAgent;
            $submission->save();

            Log::info('Form submission saved successfully', [
                'form_id' => $submissionDto->formId,
                'submission_id' => $submission->id
            ]);

            return $submission;

        } catch (Exception $e) {
            Log::error('Failed to save form submission', [
                'form_id' => $submissionDto->formId,
                'error' => $e->getMessage()
            ]);

            throw new Exception('Failed to save form submission: ' . $e->getMessage());
        }
    }

    /**
     * Generate field name for validation
     */
    private function generateFieldName(array $element): string
    {
        $fieldNameGenerator = app(FieldNameGeneratorService::class);
        return $fieldNameGenerator->generateFieldName($element);
    }

    /**
     * Create a success response
     */
    private function createSuccessResponse(FormSubmission $submission): array
    {
        return [
            'success' => true,
            'message' => 'Form submitted successfully!',
            'submission_id' => $submission->id,
            'errors' => null
        ];
    }

    /**
     * Create an error response
     */
    private function createErrorResponse(string $message, array $errors = []): array
    {
        return [
            'success' => false,
            'message' => $message,
            'submission_id' => null,
            'errors' => $errors
        ];
    }

    /**
     * Sanitize form data before storage
     */
    private function sanitizeFormData(array $data): array
    {
        $sanitized = [];
        
        foreach ($data as $field => $value) {
            if (is_string($value)) {
                // Remove potentially dangerous HTML/script tags
                $sanitized[$field] = $this->sanitizeString($value);
            } elseif (is_array($value)) {
                // Recursively sanitize arrays
                $sanitized[$field] = $this->sanitizeFormData($value);
            } else {
                // Keep other types as-is (numbers, booleans, etc.)
                $sanitized[$field] = $value;
            }
        }
        
        return $sanitized;
    }

    /**
     * Sanitize a string value
     */
    private function sanitizeString(string $value): string
    {
        // Remove HTML tags except for basic formatting
        $allowedTags = '<p><br><strong><em><u><ol><ul><li>';
        $value = strip_tags($value, $allowedTags);
        
        // Remove null bytes and other control characters
        $value = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $value);
        
        // Trim whitespace
        $value = trim($value);
        
        return $value;
    }

    /**
     * Sanitize data for logging (remove sensitive information)
     */
    private function sanitizeDataForLogging(array $data): array
    {
        $sensitiveFields = ['password', 'password_confirmation', 'credit_card', 'ssn'];
        $sanitized = $data;

        foreach ($sensitiveFields as $field) {
            if (isset($sanitized[$field])) {
                $sanitized[$field] = '[REDACTED]';
                }
            }

        return $sanitized;
    }

    /**
     * Handle rate limiting for form submissions
     */
    public function checkRateLimit(Form $form, string $ipAddress): bool
    {
        $formDto = DTOFactory::createFormDTO($form);
        return $this->checkRateLimitWithDTO($formDto, $ipAddress);
    }

    /**
     * Handle rate limiting for form submissions using DTO
     */
    private function checkRateLimitWithDTO(FormDTO $formDto, string $ipAddress): bool
    {
        $maxSubmissions = config('forms.rate_limiting.max_submissions_per_hour', 10);
        $timeWindow = now()->subHour();

        $form = Form::find($formDto->id);
        if (!$form) {
            return false;
        }

        $recentSubmissions = $form->submissions()
            ->where('ip_address', $ipAddress)
            ->where('created_at', '>=', $timeWindow)
            ->count();

        return $recentSubmissions < $maxSubmissions;
    }

    /**
     * Get rate limit information
     */
    public function getRateLimitInfo(Form $form, string $ipAddress): array
    {
        $formDto = DTOFactory::createFormDTO($form);
        return $this->getRateLimitInfoWithDTO($formDto, $ipAddress);
    }

    /**
     * Get rate limit information using DTO
     */
    private function getRateLimitInfoWithDTO(FormDTO $formDto, string $ipAddress): array
    {
        $maxSubmissions = config('forms.rate_limiting.max_submissions_per_hour', 10);
        $timeWindow = now()->subHour();

        $form = Form::find($formDto->id);
        if (!$form) {
            return [
                'current' => 0,
                'limit' => $maxSubmissions,
                'remaining' => $maxSubmissions,
                'reset_time' => now()->addHour()->startOfHour()
            ];
        }

        $recentSubmissions = $form->submissions()
            ->where('ip_address', $ipAddress)
            ->where('created_at', '>=', $timeWindow)
            ->count();

        return [
            'current' => $recentSubmissions,
            'limit' => $maxSubmissions,
            'remaining' => max(0, $maxSubmissions - $recentSubmissions),
            'reset_time' => now()->addHour()->startOfHour()
        ];
    }
}
