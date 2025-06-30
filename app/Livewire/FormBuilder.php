<?php

namespace App\Livewire;

use App\Enums\FormElementType;
use App\Models\Form;
use App\Services\FormBuilder\ElementFactory;
use App\Services\FormBuilder\ElementManager;
use App\Services\FormBuilder\IconService;
use App\Services\FormBuilder\ValidationService;
use App\Traits\WithConfirmationModal;
use App\Traits\WithToastNotifications;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

#[Layout('components.layouts.editors')]
class FormBuilder extends Component
{
    use WithConfirmationModal, WithToastNotifications;

    public Form $form;

    public array $elements = [];

    public array $settings = [];

    public ?string $selectedElementId = null;

    public string $activeBreakpoint = 'desktop';

    public string $tab = 'toolbox';

    private ElementManager $elementManager;

    private ValidationService $validationService;

    private IconService $iconService;

    private ElementFactory $elementFactory;

    public function boot(
        ElementManager $elementManager,
        ValidationService $validationService,
        IconService $iconService,
        ElementFactory $elementFactory
    ) {
        $this->elementManager = $elementManager;
        $this->validationService = $validationService;
        $this->iconService = $iconService;
        $this->elementFactory = $elementFactory;
    }

    public function mount(Form $form)
    {
        $this->form = $form;
        $this->elements = $form->elements ?? [];

        // Ensure all elements have an order field
        foreach ($this->elements as $index => $element) {
            if (! isset($element['order'])) {
                $this->elements[$index]['order'] = $index;
            }
        }

        $this->settings = $form->settings ?? config('forms.builder.default_settings');
    }

    public function addElement(string $type)
    {
        $elementType = FormElementType::tryFrom($type);
        if (! $elementType) {
            return;
        }

        $this->elementManager->addElement($this->elements, $type);
    }

    #[On('deleteElement')]
    public function deleteElement(string $elementId): void
    {
        $this->elementManager->deleteElement($this->elements, $elementId);
        $this->selectedElementId = null;
        $this->showSuccessToast('Element deleted.');
    }

    public function handleReorder($orderedOrders)
    {
        if (is_array($orderedOrders)) {
            $this->elementManager->reorderElements($this->elements, $orderedOrders);
        }
    }

    public function save()
    {
        $this->form->update([
            'elements' => $this->elements,
            'settings' => $this->settings,
        ]);

        $this->showSuccessToast('Form saved successfully!');
    }

    public function updatedElements($value, $key)
    {
        // Handle any additional logic for element updates if needed
    }

    public function updateElementWidth(string $elementId, string $breakpoint, string $width): void
    {
        $this->elementManager->updateElementWidth($this->elements, $elementId, $breakpoint, $width);
    }

    public function updateValidationRules(string $elementId, array $rules): void
    {
        $this->validationService->updateValidationRules($this->elements, $elementId, $rules);
    }

    public function updateValidationMessage(string $elementId, string $rule, string $message): void
    {
        $this->validationService->updateValidationMessage($this->elements, $elementId, $rule, $message);
    }

    public function updateValidationRuleValue(string $elementId, string $rule, string $value): void
    {
        $this->validationService->updateValidationRuleValue($this->elements, $elementId, $rule, $value);
    }

    #[Computed]
    public function selectedElement()
    {
        if ($this->selectedElementId === null) {
            return null;
        }

        return $this->elementManager->findElement($this->elements, $this->selectedElementId);
    }

    #[Computed]
    public function selectedElementIndex()
    {
        if ($this->selectedElementId === null) {
            return null;
        }

        return $this->elementManager->findElementIndex($this->elements, $this->selectedElementId);
    }

    #[Computed]
    public function selectedElementOptions(): array
    {
        if (! $this->selectedElement || $this->selectedElement['type'] !== 'select') {
            return [];
        }

        $options = $this->elements[$this->selectedElementIndex()]['properties']['options'] ?? '';

        if (is_array($options)) {
            return $options;
        }

        return array_filter(explode(PHP_EOL, $options));
    }

    #[Computed]
    public function availableValidationRules(): array
    {
        return $this->validationService->getAvailableRules();
    }

    #[Computed]
    public function availableIcons(): array
    {
        return $this->iconService->getAvailableIcons();
    }

    public function generateValidationRules(array $element): array
    {
        return $this->validationService->generateRules($element);
    }

    public function generateValidationMessages(array $element): array
    {
        return $this->validationService->generateMessages($element);
    }

    public function render()
    {
        return view('livewire.form-builder', [
            'elementTypes' => FormElementType::cases(),
            'renderedElements' => collect($this->elements)->map(fn ($element) => $this->elementFactory->renderElement($element)
            ),
        ]);
    }
}
