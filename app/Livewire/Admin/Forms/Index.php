<?php

namespace App\Livewire\Admin\Forms;

use App\Models\Form;
use Flux\Flux;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use App\Services\FormBuilder\PrebuiltForms\PrebuiltFormRegistry;
use Illuminate\Support\Str;

#[Layout('components.layouts.app')]
class Index extends Component
{
    use WithPagination;

    public bool $showCreateModal = false;

    public string $newFormName = '';

    public ?string $selectedPrebuiltForm = null;

    public function openCreateModal(): void
    {
        $this->reset('newFormName');
        $this->resetErrorBag();
        Flux::modal('create-form')->show();
    }

    public function create()
    {
        $this->validate([
            'newFormName' => ['required', 'string', 'min:3', 'max:255'],
        ]);

        $form = new Form([
            'user_id' => auth()->id(),
        ]);

        $form->setTranslation('name', 'en', $this->newFormName);

        // If a prebuilt form is selected, use its elements/settings
        if ($this->selectedPrebuiltForm) {
            $prebuilt = PrebuiltFormRegistry::find($this->selectedPrebuiltForm);
            if ($prebuilt) {
                $elements = $prebuilt->getElements();
                foreach ($elements as $i => &$element) {
                    if (!isset($element['id'])) {
                        $element['id'] = (string) Str::uuid();
                    }
                    $element['order'] = $i;
                }
                $form->elements = $elements;
                $form->settings = $prebuilt->getSettings();
            }
        }

        $form->save();

        Flux::modal('create-form')->close();

        $this->redirect(route('admin.forms.edit', $form));
    }

    #[\Livewire\Attributes\Computed]
    public function availablePrebuiltForms(): array
    {
        return PrebuiltFormRegistry::all();
    }

    public function render()
    {
        $forms = Form::where('user_id', auth()->id())
            ->latest()
            ->paginate(10);

        return view('livewire.admin.forms.index', [
            'forms' => $forms,
        ])->title('Forms');
    }
}
