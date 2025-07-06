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

/**
 * Livewire component for displaying and managing revision history.
 *
 * This component provides a comprehensive interface for viewing
 * revision history, comparing revisions, and reverting to previous states.
 */
class RevisionHistory extends Component
{
    use WithPagination, WithConfirmationModal, WithToastNotifications;

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
        if (!$this->selectedRevisionId) {
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
        if (!$this->compareRevisionId) {
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

        if (!$selected || !$compare) {
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
        if (!$this->selectedRevisionId) {
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
        $this->showConfirmationModal(
            __('revisions.confirm_revert.title'),
            __('revisions.confirm_revert.message', ['version' => $revision->formatted_version])
        );
    }

    /**
     * Revert to the selected revision.
     */
    public function revertToRevision(): void
    {
        if (!$this->revertingRevision) {
            return;
        }

        try {
            $revertAction = app(RevertToRevisionAction::class);
            $success = $revertAction->execute($this->model, $this->revertingRevision);

            if ($success) {
                $this->showSuccessToast(__('revisions.reverted_successfully'));
                $this->closeConfirmationModal();
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
     * Render the component.
     */
    public function render()
    {
        return view('livewire.admin.revision-history');
    }
} 