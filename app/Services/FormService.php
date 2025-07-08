<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\FormDTO;
use App\DTOs\FormSubmissionDTO;
use App\DTOs\DTOFactory;
use App\Models\Form;
use App\Models\FormSubmission;
use App\Services\Contracts\FormServiceInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;

/**
 * Service for handling form operations using DTOs.
 *
 * This service provides type-safe form management operations
 * using Data Transfer Objects for data integrity and consistency.
 */
class FormService implements FormServiceInterface
{
    /**
     * Create a new form.
     *
     * @param FormDTO $formDto The form data
     * @return Form The created form
     * @throws InvalidArgumentException If the DTO is invalid
     */
    public function createForm(FormDTO $formDto): Form
    {
        if (!$formDto->isValid()) {
            throw new InvalidArgumentException('Invalid form data: ' . $formDto->getValidationErrorsAsString());
        }

        try {
            DB::beginTransaction();

            $form = new Form();
            $form->user_id = $formDto->userId;
            $form->name = $formDto->name;
            $form->elements = $formDto->elements;
            $form->settings = $formDto->settings;
            $form->save();

            DB::commit();

            Log::info('Form created successfully', [
                'form_id' => $form->id,
                'user_id' => $form->user_id,
                'name' => $formDto->getNameForLocale(),
            ]);

            return $form;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create form', [
                'error' => $e->getMessage(),
                'form_data' => $formDto->toArray(),
            ]);
            throw $e;
        }
    }

    /**
     * Update an existing form.
     *
     * @param Form $form The form to update
     * @param FormDTO $formDto The updated form data
     * @return Form The updated form
     * @throws InvalidArgumentException If the DTO is invalid
     */
    public function updateForm(Form $form, FormDTO $formDto): Form
    {
        if (!$formDto->isValid()) {
            throw new InvalidArgumentException('Invalid form data: ' . $formDto->getValidationErrorsAsString());
        }

        try {
            DB::beginTransaction();

            $form->name = $formDto->name;
            $form->elements = $formDto->elements;
            $form->settings = $formDto->settings;
            $form->save();

            DB::commit();

            Log::info('Form updated successfully', [
                'form_id' => $form->id,
                'name' => $formDto->getNameForLocale(),
            ]);

            return $form;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update form', [
                'form_id' => $form->id,
                'error' => $e->getMessage(),
                'form_data' => $formDto->toArray(),
            ]);
            throw $e;
        }
    }

    /**
     * Delete a form.
     *
     * @param Form $form The form to delete
     * @return bool True if deleted successfully
     */
    public function deleteForm(Form $form): bool
    {
        try {
            DB::beginTransaction();

            // Delete all submissions first
            $form->submissions()->delete();
            
            // Delete the form
            $form->delete();

            DB::commit();

            Log::info('Form deleted successfully', [
                'form_id' => $form->id,
                'name' => $form->getCurrentName(),
            ]);

            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete form', [
                'form_id' => $form->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Get a form by ID.
     *
     * @param int $id The form ID
     * @return FormDTO|null The form DTO or null if not found
     */
    public function getFormById(int $id): ?FormDTO
    {
        $form = Form::find($id);
        
        if (!$form) {
            return null;
        }

        return DTOFactory::createFormDTO($form);
    }

    /**
     * Get all forms with pagination.
     *
     * @param int $perPage The number of forms per page
     * @param array<string, mixed> $filters Optional filters
     * @return LengthAwarePaginator The paginated forms
     */
    public function getFormsPaginated(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = Form::with('user');

        // Apply filters
        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search): void {
                $q->whereRaw("JSON_EXTRACT(name, '$.en') LIKE ?", ["%{$search}%"])
                  ->orWhereRaw("JSON_EXTRACT(name, '$.fr') LIKE ?", ["%{$search}%"]);
            });
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    /**
     * Get forms by user ID.
     *
     * @param int $userId The user ID
     * @return array<FormDTO> The user's forms
     */
    public function getFormsByUserId(int $userId): array
    {
        $forms = Form::where('user_id', $userId)->get();
        return DTOFactory::createFormDTOs($forms);
    }

    /**
     * Create a form submission.
     *
     * @param FormSubmissionDTO $submissionDto The submission data
     * @return FormSubmission The created submission
     * @throws InvalidArgumentException If the DTO is invalid
     */
    public function createSubmission(FormSubmissionDTO $submissionDto): FormSubmission
    {
        if (!$submissionDto->isValid()) {
            throw new InvalidArgumentException('Invalid submission data: ' . $submissionDto->getValidationErrorsAsString());
        }

        try {
            DB::beginTransaction();

            $submission = new FormSubmission();
            $submission->form_id = $submissionDto->formId;
            $submission->data = $submissionDto->data;
            $submission->ip_address = $submissionDto->ipAddress;
            $submission->user_agent = $submissionDto->userAgent;
            $submission->save();

            DB::commit();

            Log::info('Form submission created successfully', [
                'submission_id' => $submission->id,
                'form_id' => $submission->form_id,
                'ip_address' => $submissionDto->ipAddress,
            ]);

            return $submission;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create form submission', [
                'error' => $e->getMessage(),
                'submission_data' => $submissionDto->toArray(),
            ]);
            throw $e;
        }
    }

    /**
     * Get submissions for a form.
     *
     * @param int $formId The form ID
     * @param int $perPage The number of submissions per page
     * @return LengthAwarePaginator The paginated submissions
     */
    public function getFormSubmissions(int $formId, int $perPage = 15): LengthAwarePaginator
    {
        $submissions = FormSubmission::where('form_id', $formId)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        // Convert to DTOs
        $submissions->getCollection()->transform(fn($submission): \App\DTOs\FormSubmissionDTO => DTOFactory::createFormSubmissionDTO($submission));

        return $submissions;
    }

    /**
     * Get a submission by ID.
     *
     * @param int $id The submission ID
     * @return FormSubmissionDTO|null The submission DTO or null if not found
     */
    public function getSubmissionById(int $id): ?FormSubmissionDTO
    {
        $submission = FormSubmission::with('form')->find($id);
        
        if (!$submission) {
            return null;
        }

        return DTOFactory::createFormSubmissionDTO($submission);
    }

    /**
     * Delete a submission.
     *
     * @param FormSubmission $submission The submission to delete
     * @return bool True if deleted successfully
     */
    public function deleteSubmission(FormSubmission $submission): bool
    {
        try {
            $submission->delete();

            Log::info('Form submission deleted successfully', [
                'submission_id' => $submission->id,
                'form_id' => $submission->form_id,
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Failed to delete form submission', [
                'submission_id' => $submission->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Get form statistics.
     *
     * @param int $formId The form ID
     * @return array<string, mixed> The form statistics
     */
    public function getFormStatistics(int $formId): array
    {
        $form = Form::find($formId);
        
        if (!$form) {
            return [];
        }

        $totalSubmissions = $form->submissions()->count();
        $recentSubmissions = $form->submissions()
            ->where('created_at', '>=', now()->subDays(7))
            ->count();

        $elementTypes = collect($form->elements ?? [])
            ->pluck('type')
            ->countBy()
            ->toArray();

        return [
            'total_submissions' => $totalSubmissions,
            'recent_submissions' => $recentSubmissions,
            'element_types' => $elementTypes,
            'element_count' => count($form->elements ?? []),
            'has_file_uploads' => collect($form->elements ?? [])->contains('type', 'file'),
        ];
    }

    /**
     * Validate form data against form configuration.
     *
     * @param FormDTO $formDto The form DTO
     * @param array<string, mixed> $data The form data to validate
     * @return array<string, string> Validation errors, empty if valid
     */
    public function validateFormData(FormDTO $formDto, array $data): array
    {
        $errors = [];

        // Check required fields
        $requiredFields = $formDto->getRequiredFields();
        foreach ($requiredFields as $fieldName) {
            if (!isset($data[$fieldName]) || empty($data[$fieldName])) {
                $errors[$fieldName] = __('forms.validation.field_required');
            }
        }

        // Check for unexpected fields
        $expectedFields = $formDto->getFieldNames();
        foreach (array_keys($data) as $fieldName) {
            if (!in_array($fieldName, $expectedFields)) {
                $errors[$fieldName] = __('forms.validation.unexpected_field');
            }
        }

        return $errors;
    }

    /**
     * Duplicate a form.
     *
     * @param Form $originalForm The form to duplicate
     * @param int|null $newUserId The user ID for the new form
     * @return FormDTO The duplicated form DTO
     */
    public function duplicateForm(Form $originalForm, ?int $newUserId = null): FormDTO
    {
        try {
            DB::beginTransaction();

            $newForm = new Form();
            $newForm->user_id = $newUserId ?? $originalForm->user_id;
            
            // Duplicate name with "(Copy)" suffix
            $originalName = $originalForm->getCurrentName();
            $newName = [];
            foreach ($originalName as $locale => $name) {
                $newName[$locale] = $name . ' (Copy)';
            }
            $newForm->name = $newName;
            
            $newForm->elements = $originalForm->elements;
            $newForm->settings = $originalForm->settings;
            $newForm->save();

            DB::commit();

            Log::info('Form duplicated successfully', [
                'original_form_id' => $originalForm->id,
                'new_form_id' => $newForm->id,
            ]);

            return DTOFactory::createFormDTO($newForm);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to duplicate form', [
                'original_form_id' => $originalForm->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Get all forms with optional filtering.
     *
     * @param array<string, mixed> $filters Optional filters to apply
     * @return Collection<Form> The collection of forms
     */
    public function getForms(array $filters = []): Collection
    {
        $query = Form::with('user');

        // Apply filters
        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search): void {
                $q->whereRaw("JSON_EXTRACT(name, '$.en') LIKE ?", ["%{$search}%"])
                  ->orWhereRaw("JSON_EXTRACT(name, '$.fr') LIKE ?", ["%{$search}%"]);
            });
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    /**
     * Get forms by user ID.
     *
     * @param int $userId The user ID
     * @return Collection<Form> The collection of forms
     */
    public function getFormsByUser(int $userId): Collection
    {
        return Form::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Generate validation rules for a form element.
     *
     * @param array<string, mixed> $element The form element configuration
     * @return array<string> Array of validation rules
     */
    public function generateValidationRules(array $element): array
    {
        $rules = [];

        $rules[] = !empty($element['required']) && $element['required'] === true ? 'required' : 'nullable';

        // Add type-specific rules
        switch ($element['type'] ?? '') {
            case 'email':
                $rules[] = 'email';
                break;
            case 'number':
                $rules[] = 'numeric';
                if (!empty($element['min'])) {
                    $rules[] = 'min:' . $element['min'];
                }
                if (!empty($element['max'])) {
                    $rules[] = 'max:' . $element['max'];
                }
                break;
            case 'file':
                $rules[] = 'file';
                if (!empty($element['max_size'])) {
                    $rules[] = 'max:' . $element['max_size'];
                }
                if (!empty($element['allowed_types'])) {
                    $rules[] = 'mimes:' . implode(',', $element['allowed_types']);
                }
                break;
            case 'textarea':
                if (!empty($element['max_length'])) {
                    $rules[] = 'max:' . $element['max_length'];
                }
                break;
        }

        return $rules;
    }

    /**
     * Generate validation messages for a form element.
     *
     * @param array<string, mixed> $element The form element configuration
     * @return array<string, string> Array of validation messages
     */
    public function generateValidationMessages(array $element): array
    {
        $messages = [];
        $fieldName = $element['name'] ?? '';

        if (!empty($element['required']) && $element['required'] === true) {
            $messages[$fieldName . '.required'] = __('forms.validation.field_required');
        }

        // Add type-specific messages
        switch ($element['type'] ?? '') {
            case 'email':
                $messages[$fieldName . '.email'] = __('forms.validation.email_invalid');
                break;
            case 'number':
                $messages[$fieldName . '.numeric'] = __('forms.validation.numeric_required');
                if (!empty($element['min'])) {
                    $messages[$fieldName . '.min'] = __('forms.validation.min_value', ['min' => $element['min']]);
                }
                if (!empty($element['max'])) {
                    $messages[$fieldName . '.max'] = __('forms.validation.max_value', ['max' => $element['max']]);
                }
                break;
            case 'file':
                $messages[$fieldName . '.file'] = __('forms.validation.file_required');
                if (!empty($element['max_size'])) {
                    $messages[$fieldName . '.max'] = __('forms.validation.file_too_large');
                }
                if (!empty($element['allowed_types'])) {
                    $messages[$fieldName . '.mimes'] = __('forms.validation.file_type_not_allowed');
                }
                break;
            case 'textarea':
                if (!empty($element['max_length'])) {
                    $messages[$fieldName . '.max'] = __('forms.validation.text_too_long', ['max' => $element['max_length']]);
                }
                break;
        }

        return $messages;
    }

    /**
     * Get form field names from form elements.
     *
     * @param FormDTO $formDto The form DTO
     * @return array<string> Array of field names
     */
    public function getFormFieldNames(FormDTO $formDto): array
    {
        return $formDto->getFieldNames();
    }

    /**
     * Check if a form has file upload elements.
     *
     * @param FormDTO $formDto The form DTO
     * @return bool True if the form has file upload elements
     */
    public function hasFileUploads(FormDTO $formDto): bool
    {
        return collect($formDto->elements)->contains('type', 'file');
    }

    /**
     * Get required fields from a form.
     *
     * @param FormDTO $formDto The form DTO
     * @return array<string> Array of required field names
     */
    public function getRequiredFields(FormDTO $formDto): array
    {
        return $formDto->getRequiredFields();
    }
} 