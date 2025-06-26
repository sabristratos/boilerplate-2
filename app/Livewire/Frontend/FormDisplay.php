<?php

namespace App\Livewire\Frontend;

use App\Models\Form;
use App\Services\FormFieldValidator;
use Livewire\Component;
use App\Mail\FormSubmissionNotification;
use Flux\Flux;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\On;

class FormDisplay extends Component
{
    public Form $form;
    public array $formData = [];
    public bool $preview = false;

    #[On('form-updated')]
    public function refreshForm(): void
    {
        $this->form = $this->form->fresh('formFields');
    }

    public function mount(Form $form, bool $preview = false)
    {
        $this->form = $form;
        $this->preview = $preview;
        foreach ($this->form->formFields as $field) {
            $this->formData[$field->name] = '';
        }
    }

    public function submit(FormFieldValidator $validator)
    {
        if ($this->preview) {
            return;
        }

        $this->validate(
            $validator->getRules($this->form),
            [],
            $validator->getAttributes($this->form)
        );

        try {
            $submission = $this->form->formSubmissions()->create([
                'data' => $this->formData,
            ]);

            if ($this->form->send_notification && $this->form->recipient_email) {
                Mail::to($this->form->recipient_email)->send(new FormSubmissionNotification($submission));
            }

            Flux::toast(text: $this->form->getTranslation('success_message', app()->getLocale()), variant: 'success');

            $this->formData = [];
        } catch (\Exception $e) {
            Log::error('Form submission failed: ' . $e->getMessage());
            Flux::toast(text: __('forms.toast_submission_error'), variant: 'danger');
        }
    }

    public function render()
    {
        if ($this->preview) {
            $this->formData = [];
            foreach ($this->form->formFields as $field) {
                $this->formData[$field->name] = '';
            }
        }

        return view('livewire.frontend.form-display');
    }
}
