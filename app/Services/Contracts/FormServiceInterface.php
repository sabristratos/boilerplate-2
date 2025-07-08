<?php

declare(strict_types=1);

namespace App\Services\Contracts;

use App\DTOs\FormDTO;
use App\Models\Form;
use Illuminate\Database\Eloquent\Collection;

/**
 * Interface for form service operations.
 *
 * This interface defines the contract for form-related business logic,
 * including CRUD operations, validation, and form management.
 */
interface FormServiceInterface
{
    /**
     * Create a new form from a DTO.
     *
     * @param FormDTO $formDto The form data transfer object
     * @return Form The created form
     * @throws \InvalidArgumentException If the DTO is invalid
     */
    public function createForm(FormDTO $formDto): Form;

    /**
     * Update an existing form from a DTO.
     *
     * @param Form $form The form to update
     * @param FormDTO $formDto The form data transfer object
     * @return Form The updated form
     * @throws \InvalidArgumentException If the DTO is invalid
     */
    public function updateForm(Form $form, FormDTO $formDto): Form;

    /**
     * Delete a form.
     *
     * @param Form $form The form to delete
     * @return bool True if the form was deleted successfully
     */
    public function deleteForm(Form $form): bool;

    /**
     * Get a form by ID.
     *
     * @param int $id The form ID
     * @return FormDTO|null The form DTO or null if not found
     */
    public function getFormById(int $id): ?FormDTO;

    /**
     * Get all forms with optional filtering.
     *
     * @param array<string, mixed> $filters Optional filters to apply
     * @return Collection<Form> The collection of forms
     */
    public function getForms(array $filters = []): Collection;

    /**
     * Get forms by user ID.
     *
     * @param int $userId The user ID
     * @return Collection<Form> The collection of forms
     */
    public function getFormsByUser(int $userId): Collection;

    /**
     * Validate form data against form configuration.
     *
     * @param FormDTO $formDto The form DTO containing validation rules
     * @param array<string, mixed> $data The data to validate
     * @return array<string, string> Array of validation errors (empty if valid)
     */
    public function validateFormData(FormDTO $formDto, array $data): array;

    /**
     * Generate validation rules for a form element.
     *
     * @param array<string, mixed> $element The form element configuration
     * @return array<string> Array of validation rules
     */
    public function generateValidationRules(array $element): array;

    /**
     * Generate validation messages for a form element.
     *
     * @param array<string, mixed> $element The form element configuration
     * @return array<string, string> Array of validation messages
     */
    public function generateValidationMessages(array $element): array;

    /**
     * Get form field names from form elements.
     *
     * @param FormDTO $formDto The form DTO
     * @return array<string> Array of field names
     */
    public function getFormFieldNames(FormDTO $formDto): array;

    /**
     * Check if a form has file upload elements.
     *
     * @param FormDTO $formDto The form DTO
     * @return bool True if the form has file upload elements
     */
    public function hasFileUploads(FormDTO $formDto): bool;

    /**
     * Get required fields from a form.
     *
     * @param FormDTO $formDto The form DTO
     * @return array<string> Array of required field names
     */
    public function getRequiredFields(FormDTO $formDto): array;
} 