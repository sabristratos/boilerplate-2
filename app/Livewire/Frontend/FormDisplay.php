<?php

namespace App\Livewire\Frontend;

use App\Models\Form;
use App\Services\FormBuilder\ElementFactory;
use Livewire\Component;
use Livewire\WithFileUploads;

class FormDisplay extends Component
{
    use WithFileUploads;

    public Form $form;

    public array $formData = [];

    public bool $submitted = false;

    public string $successMessage = '';

    private ElementFactory $elementFactory;

    public function boot(ElementFactory $elementFactory)
    {
        $this->elementFactory = $elementFactory;
    }

    public function mount($form)
    {
        $this->form = $form;
        $this->initializeFormData();
    }

    private function initializeFormData()
    {
        $this->formData = [];

        if ($this->form->elements) {
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
        $renderedElements = [];

        if ($this->form->elements) {
            foreach ($this->form->elements as $element) {
                $fieldName = $this->generateFieldName($element);
                $renderedElements[] = $this->elementFactory->renderElement($element, 'preview', $fieldName);
            }
        }

        return view('livewire.frontend.form-display', [
            'renderedElements' => $renderedElements,
        ]);
    }
}
