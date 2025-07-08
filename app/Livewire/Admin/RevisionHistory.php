<?php

declare(strict_types=1);

namespace App\Livewire\Admin;

use App\Actions\Revisions\RevertToRevisionAction;
use App\Models\Revision;
use App\Services\RevisionService;
use App\Traits\WithConfirmationModal;
use App\Traits\WithToastNotifications;
use Illuminate\Database\Eloquent\Model;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;

/**
 * Livewire component for displaying and managing revision history.
 *
 * This component provides a comprehensive interface for viewing
 * revision history, comparing revisions, and reverting to previous states.
 */
class RevisionHistory extends Component
{
    use WithConfirmationModal, WithPagination, WithToastNotifications;

    /**
     * The model to show revisions for.
     */
    public Model $model;

    /**
     * The selected revision for comparison.
     */
    public ?int $selectedRevisionId = null;

    /**
     * The revision to compare against.
     */
    public ?int $compareRevisionId = null;

    /**
     * Whether to show the comparison view.
     */
    public bool $showComparison = false;

    /**
     * The revision being reverted to.
     */
    public ?Revision $revertingRevision = null;

    /**
     * Whether to show the field details modal.
     */
    public bool $showFieldDetails = false;

    /**
     * The field details label.
     */
    public string $fieldDetailsLabel = '';

    /**
     * The field details value (formatted).
     */
    public string $fieldDetailsValue = '';

    /**
     * Mount the component.
     */
    public function mount(Model $model): void
    {
        $this->model = $model;
    }

    /**
     * Get the revisions for the model.
     */
    #[Computed]
    public function getRevisionsProperty()
    {
        return $this->model->revisions()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
    }

    /**
     * Get the selected revision.
     */
    #[Computed]
    public function getSelectedRevisionProperty(): ?Revision
    {
        if ($this->selectedRevisionId === null || $this->selectedRevisionId === 0) {
            return null;
        }

        return $this->model->revisions()->find($this->selectedRevisionId);
    }

    /**
     * Get the comparison revision.
     */
    #[Computed]
    public function getCompareRevisionProperty(): ?Revision
    {
        if ($this->compareRevisionId === null || $this->compareRevisionId === 0) {
            return null;
        }

        return $this->model->revisions()->find($this->compareRevisionId);
    }

    /**
     * Get the differences between selected and comparison revisions.
     */
    #[Computed]
    public function getDifferencesProperty(): array
    {
        $selected = $this->selectedRevision;
        $compare = $this->compareRevision;

        if (! $selected || ! $compare) {
            return [];
        }

        $revisionService = app(RevisionService::class);

        return $revisionService->compareRevisions($selected, $compare);
    }

    /**
     * Select a revision for comparison.
     */
    public function selectRevision(int $revisionId): void
    {
        $this->selectedRevisionId = $revisionId;
        $this->showComparison = false;
    }

    /**
     * Start comparison mode.
     */
    public function startComparison(): void
    {
        if ($this->selectedRevisionId === null || $this->selectedRevisionId === 0) {
            $this->showErrorToast(__('revisions.errors.no_revision_selected'));

            return;
        }

        $this->showComparison = true;
    }

    /**
     * Set the comparison revision.
     */
    public function setCompareRevision(int $revisionId): void
    {
        $this->compareRevisionId = $revisionId;
    }

    /**
     * Clear the comparison.
     */
    public function clearComparison(): void
    {
        $this->compareRevisionId = null;
        $this->showComparison = false;
    }

    /**
     * Show the revert confirmation modal.
     */
    public function showRevertConfirmation(Revision $revision): void
    {
        $this->revertingRevision = $revision;
        $this->confirmAction(
            __('revisions.confirm_revert.title'),
            __('revisions.confirm_revert.message', ['version' => $revision->formatted_version]),
            'revertToRevision'
        );
    }

    /**
     * Revert to the selected revision.
     */
    #[On('revertToRevision')]
    public function revertToRevision(): void
    {
        if (!$this->revertingRevision instanceof \App\Models\Revision) {
            return;
        }

        try {
            $revertAction = app(RevertToRevisionAction::class);
            $success = $revertAction->execute($this->model, $this->revertingRevision);

            if ($success) {
                $this->showSuccessToast(__('revisions.reverted_successfully'));
                $this->revertingRevision = null;
                $this->dispatch('model-updated');
            } else {
                $this->showErrorToast(__('revisions.revert_failed'));
            }
        } catch (\Exception $e) {
            $this->showErrorToast(__('revisions.revert_failed'), $e->getMessage());
        }
    }

    /**
     * Show field details in a modal.
     */
    #[On('show-field-details')]
    public function showFieldDetails(array $data): void
    {
        $field = $data['field'] ?? '';
        $value = $data['value'] ?? null;

        $this->fieldDetailsLabel = getRevisionFieldLabel($field);
        
        // Format the value for display
        if (is_array($value) || is_object($value)) {
            $this->fieldDetailsValue = json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        } else {
            $this->fieldDetailsValue = (string) $value;
        }

        $this->showFieldDetails = true;
    }

    /**
     * Render the component.
     */
    public function render()
    {
        return view('livewire.admin.revision-history');
    }
}
