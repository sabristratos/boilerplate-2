<?php

namespace App\Livewire\Frontend;

use App\Models\Form;
use App\Services\FormBuilder\ElementFactory;
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

    public function boot(ElementFactory $elementFactory)
    {
        $this->elementFactory = $elementFactory;
    }

    public function mount($form = null)
    {
        // Handle both Form object and form ID
        if (is_numeric($form) || (is_string($form) && ctype_digit($form))) {
            // If it's a numeric ID (as number or string), find the form
            $formId = (int) $form;
            $this->form = Form::find($formId);
            
            if (!$this->form) {
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

    public function submit()
    {
        if (!$this->form) {
            $this->addError('general', 'Form not found.');
            return;
        }

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
    }

    public function render()
    {
        if (!$this->form) {
            return view('livewire.frontend.form-display', [
                'renderedElements' => [],
                'error' => 'Form not found.'
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
                    'trace' => $e->getTraceAsString()
                ]);
                
                return view('livewire.frontend.form-display', [
                    'renderedElements' => [],
                    'error' => 'Error rendering form elements: ' . $e->getMessage()
                ]);
            }
        }

        return view('livewire.frontend.form-display', [
            'renderedElements' => $renderedElements,
        ]);
    }
}
