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

    public function mount($formId)
    {
        $this->form = Form::findOrFail($formId);
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
        $label = $element['properties']['label'] ?? 'field';
        return 'field_' . $element['id'];
    }

    public function submit()
    {
        // Generate validation rules from form elements
        $rules = $this->generateValidationRules();
        
        // Validate the form data
        $this->validate($rules);
        
        // Save the form submission
        $this->form->submissions()->create([
            'data' => $this->formData,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
        
        $this->submitted = true;
        $this->successMessage = 'Form submitted successfully!';
        
        // Reset form data
        $this->initializeFormData();
    }

    private function generateValidationRules(): array
    {
        $rules = [];
        
        if ($this->form->elements) {
            foreach ($this->form->elements as $element) {
                $fieldName = $this->generateFieldName($element);
                $elementRules = [];
                
                // Add validation rules based on element configuration
                if (isset($element['validation']['rules'])) {
                    foreach ($element['validation']['rules'] as $ruleKey) {
                        $rule = $element['validation']['values'][$ruleKey] ?? null;
                        
                        if ($rule) {
                            $elementRules[] = $rule;
                        } else {
                            $elementRules[] = $ruleKey;
                        }
                    }
                }
                
                if (!empty($elementRules)) {
                    $rules[$fieldName] = $elementRules;
                }
            }
        }
        
        return $rules;
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