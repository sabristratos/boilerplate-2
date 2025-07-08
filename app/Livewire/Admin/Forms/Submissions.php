<?php

declare(strict_types=1);

namespace App\Livewire\Admin\Forms;

use App\DTOs\FormSubmissionDTO;
use App\DTOs\DTOFactory;
use App\Models\Form;
use App\Services\Contracts\FormServiceInterface;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Livewire component for managing form submissions.
 *
 * This component provides a list view of form submissions with search,
 * pagination, and sorting capabilities. It uses DTOs and services for
 * data handling and business logic.
 */
#[Layout('components.layouts.app')]
class Submissions extends Component
{
    use WithPagination;

    public Form $form;

    public string $search = '';

    public int $perPage = 10;

    public string $sortBy = 'created_at';

    public string $sortDirection = 'desc';

    /**
     * The querystring properties.
     *
     * @var array<string, array<string, mixed>>
     */
    protected $queryString = [
        'search' => ['except' => ''],
        'perPage' => ['except' => 10],
        'sortBy' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
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
     * Mount the component with the form.
     */
    public function mount(Form $form): void
    {
        $this->form = $form;
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
     * Sort submissions by column.
     */
    public function sort(string $column): void
    {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'asc';
        }
    }

    /**
     * Delete a submission.
     */
    public function deleteSubmission(int $submissionId): void
    {
        try {
            $submission = $this->form->submissions()->findOrFail($submissionId);
            $this->formService->deleteSubmission($submission);
            
            $this->dispatch('submission-deleted');
            
        } catch (\Exception $e) {
            logger()->error('Failed to delete submission', [
                'submission_id' => $submissionId,
                'form_id' => $this->form->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Get submission DTO by ID.
     */
    public function getSubmissionDTO(int $submissionId): ?FormSubmissionDTO
    {
        return $this->formService->getSubmissionById($submissionId);
    }

    /**
     * Render the component.
     */
    public function render()
    {
        $submissions = $this->form->submissions()
            ->when($this->search, function ($query, $search): void {
                $query->where(function ($q) use ($search): void {
                    $q->where('ip_address', 'like', '%'.$search.'%')
                        ->orWhere('user_agent', 'like', '%'.$search.'%')
                        ->orWhereRaw("JSON_EXTRACT(data, '$.*') LIKE ?", ["%{$search}%"]);
                });
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);

        // Convert submissions to DTOs for the view
        $submissions->getCollection()->transform(function ($submission) {
            return DTOFactory::createFormSubmissionDTO($submission);
        });

        return view('livewire.admin.forms.submissions', [
            'submissions' => $submissions,
        ])->title(__('forms.submissions_for', ['name' => $this->form->getTranslation('name', app()->getLocale())]));
    }
}
