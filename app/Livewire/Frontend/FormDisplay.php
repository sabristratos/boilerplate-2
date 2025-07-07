<?php

namespace App\Livewire\Frontend;

use App\Models\Form;
use App\DTOs\FormDTO;
use App\DTOs\DTOFactory;
use App\Services\FormBuilder\ElementFactory;
use App\Services\FormService;
use Livewire\Component;
use Livewire\WithFileUploads;

class FormDisplay extends Component
{
    use WithFileUploads;

    public $form = null;

    public array $formData = [];

    public bool $submitted = false;

    public string $successMessage = '';

    private ElementFactory $elementFactory;
    private FormService $formService;

    /**
     * Boot the component with dependencies.
     *
     * @param ElementFactory $elementFactory
     * @param FormService $formService
     * @return void
     */
    public function boot(ElementFactory $elementFactory, FormService $formService)
    {
        $this->elementFactory = $elementFactory;
        $this->formService = $formService;
    }

    /**
     * Mount the component with the given form or form ID.
     *
     * @param mixed $form
     * @return void
     */
    public function mount($form = null)
    {
        // Handle both Form object and form ID
        if (is_numeric($form) || (is_string($form) && ctype_digit($form))) {
            // If it's a numeric ID (as number or string), find the form
            $formId = (int) $form;
            $this->form = Form::find($formId);

            if (! $this->form) {
                \Log::warning('FormDisplay: Form not found with ID', ['form_id' => $formId]);
            }
        } elseif ($form instanceof Form) {
            // If it's already a Form object
            $this->form = $form;
        } elseif (is_array($form)) {
            // If it's an array with formId
            $formId = $form['formId'] ?? $form['form_id'] ?? null;
            if ($formId) {
                $this->form = Form::find($formId);
            }
        }

        if ($this->form) {
            $this->initializeFormData();
        } else {
            \Log::warning('FormDisplay: No form loaded', ['form_parameter' => $form]);
        }
    }

    private function initializeFormData()
    {
        $this->formData = [];

        if ($this->form && $this->form->elements) {
            foreach ($this->form->elements as $element) {
                $fieldName = $this->generateFieldName($element);
                $this->formData[$fieldName] = '';
            }
        }
    }

    private function generateFieldName($element): string
    {
        $fieldNameGenerator = app(\App\Services\FormBuilder\FieldNameGeneratorService::class);

        return $fieldNameGenerator->generateFieldName($element);
    }

    /**
     * Submit the form and handle validation and submission logic.
     *
     * @return void
     */
    public function submit()
    {
        if (! $this->form) {
            $this->addError('general', __('forms.errors.form_not_found'));
            return;
        }

        try {
            // Convert form to DTO for validation
            $formDto = DTOFactory::createFormDTO($this->form);
            
            // Validate form data using the service
            $validationErrors = $this->formService->validateFormData($formDto, $this->formData);
            
            if (!empty($validationErrors)) {
                foreach ($validationErrors as $field => $message) {
                    $this->addError($field, $message);
                }
                $this->addError('general', __('forms.validation.please_correct_errors'));
                return;
            }

            // Handle submission using the error handler
            $errorHandler = app(\App\Services\FormBuilder\FormSubmissionErrorHandler::class);
            $result = $errorHandler->handleSubmission($this->form, $this->formData);

            if ($result['success']) {
                $this->submitted = true;
                $this->successMessage = $result['message'];
                $this->initializeFormData();
            } else {
                // Handle validation errors
                if (! empty($result['errors'])) {
                    foreach ($result['errors'] as $field => $messages) {
                        foreach ($messages as $message) {
                            $this->addError($field, $message);
                        }
                    }
                }

                // Show error message
                $this->addError('general', $result['message']);
            }
        } catch (\Exception $e) {
            logger()->error('Form submission error', [
                'form_id' => $this->form->id ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            $this->addError('general', __('forms.errors.form_submission_error'));
        }
    }

    /**
     * Render the form display component.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        if (! $this->form) {
                            return view('livewire.frontend.form-display', [
                    'renderedElements' => [],
                    'error' => __('forms.errors.form_not_found'),
                ]);
        }

        $renderedElements = [];

        if ($this->form->elements) {
            try {
                foreach ($this->form->elements as $element) {
                    $fieldName = $this->generateFieldName($element);
                    $renderedElements[] = $this->elementFactory->renderElement($element, 'preview', $fieldName);
                }
            } catch (\Exception $e) {
                \Log::error('FormDisplay: Error rendering elements', [
                    'form_id' => $this->form->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);

                return view('livewire.frontend.form-display', [
                    'renderedElements' => [],
                    'error' => __('forms.errors.error_rendering_elements', ['message' => $e->getMessage()]),
                ]);
            }
        }

        return view('livewire.frontend.form-display', [
            'renderedElements' => $renderedElements,
        ]);
    }
}
