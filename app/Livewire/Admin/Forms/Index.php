<?php

declare(strict_types=1);

namespace App\Livewire\Admin\Forms;

use App\DTOs\FormDTO;
use App\DTOs\DTOFactory;
use App\Enums\FormStatus;
use App\Models\Form;
use App\Services\Contracts\FormServiceInterface;
use App\Services\FormBuilder\PrebuiltForms\PrebuiltFormRegistry;
use App\Traits\WithEnumHelpers;
use App\Traits\WithToastNotifications;
use Flux\Flux;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Livewire component for managing forms in the admin area.
 *
 * This component provides a list view of forms with search, pagination,
 * and form creation capabilities. It uses DTOs and services for
 * data handling and business logic.
 */
#[Layout('components.layouts.app')]
class Index extends Component
{
    use WithPagination, WithEnumHelpers, WithToastNotifications;

    public bool $showCreateModal = false;

    public string $newFormName = '';

    public ?string $selectedPrebuiltForm = null;

    public string $search = '';

    public int $perPage = 10;

    /**
     * The querystring properties.
     *
     * @var array<string, array<string, mixed>>
     */
    protected $queryString = [
        'search' => ['except' => ''],
        'perPage' => ['except' => 10],
    ];

    /**
     * Form service instance.
     */
    protected FormServiceInterface $formService;

    /**
     * Boot the component with dependencies.
     */
    public function boot(FormServiceInterface $formService): void
    {
        $this->formService = $formService;
    }

    /**
     * Open the create form modal.
     */
    public function openCreateModal(): void
    {
        $this->reset('newFormName', 'selectedPrebuiltForm');
        $this->resetErrorBag();
        Flux::modal('create-form')->show();
    }

    /**
     * Create a new form.
     */
    public function create(): void
    {
        $this->validate([
            'newFormName' => ['required', 'string', 'min:3', 'max:255'],
        ]);

        try {
            // Prepare form data
            $name = ['en' => $this->newFormName];
            $elements = [];
            $settings = [];

            // If a prebuilt form is selected, use its elements/settings
            if ($this->selectedPrebuiltForm !== null && $this->selectedPrebuiltForm !== '' && $this->selectedPrebuiltForm !== '0') {
                $prebuilt = PrebuiltFormRegistry::find($this->selectedPrebuiltForm);
                if ($prebuilt instanceof \App\Services\FormBuilder\PrebuiltForms\PrebuiltFormInterface) {
                    $elements = $prebuilt->getElements();
                    foreach ($elements as $i => &$element) {
                        if (!isset($element['id'])) {
                            $element['id'] = (string) Str::uuid();
                        }
                        $element['order'] = $i;
                    }
                    $settings = $prebuilt->getSettings();
                }
            }

            // Create DTO for form creation
            $formDto = DTOFactory::createFormDTOForCreation(
                name: $name,
                elements: $elements,
                settings: $settings,
                userId: auth()->id()
            );

            // Create the form using the service
            $form = $this->formService->createForm($formDto);

            Flux::modal('create-form')->close();
            $this->showSuccessToast(__('forms.toast_form_created'));

            $this->redirect(route('admin.forms.edit', $form));

        } catch (\Exception $e) {
            $this->showErrorToast(__('forms.toast_form_creation_failed'));
            logger()->error('Failed to create form', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
                'form_name' => $this->newFormName,
            ]);
        }
    }

    /**
     * Duplicate an existing form.
     */
    public function duplicateForm(int $formId): void
    {
        try {
            $form = Form::findOrFail($formId);
            $newFormDto = $this->formService->duplicateForm($form, auth()->id());
            
            $this->showSuccessToast(__('forms.toast_form_duplicated'));
            $this->redirect(route('admin.forms.edit', $newFormDto->id));

        } catch (\Exception $e) {
            $this->showErrorToast(__('forms.toast_form_duplication_failed'));
            logger()->error('Failed to duplicate form', [
                'error' => $e->getMessage(),
                'form_id' => $formId,
                'user_id' => auth()->id(),
            ]);
        }
    }

    /**
     * Get available prebuilt forms.
     */
    #[\Livewire\Attributes\Computed]
    public function availablePrebuiltForms(): array
    {
        return PrebuiltFormRegistry::all();
    }

    /**
     * Get the form status enum for a form.
     */
    public function getFormStatusForForm(Form $form): FormStatus
    {
        return $form->status ?? FormStatus::DRAFT;
    }

    /**
     * Get all available form statuses for filtering.
     */
    public function getAvailableStatuses(): array
    {
        return $this->getFormStatusOptions();
    }

    /**
     * Handle search updates.
     */
    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    /**
     * Handle per page updates.
     */
    public function updatedPerPage(): void
    {
        $this->resetPage();
    }

    /**
     * Render the component.
     */
    public function render()
    {
        $filters = [
            'user_id' => auth()->id(),
            'search' => $this->search,
        ];

        $forms = $this->formService->getFormsPaginated($this->perPage, $filters);

        return view('livewire.admin.forms.index', [
            'forms' => $forms,
        ])->title(__('forms.index_title'));
    }
}
