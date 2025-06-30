<?php

namespace App\Traits;

trait WithConfirmationModal
{
    /**
     * Show a confirmation modal by dispatching a browser event.
     */
    protected function showConfirmation(string $action, array $actionParams = [], array $data = []): void
    {
        $this->dispatch('show-confirmation', data: [
            ...$data,
            'action' => $action,
            'actionParams' => $actionParams,
        ]);
    }

    /**
     * Show a delete confirmation modal.
     */
    public function confirmDelete(int|string $itemId, string $action = 'delete', array $data = []): void
    {
        $this->showConfirmation($action, [$itemId], [
            'title' => __('messages.delete_confirm_title'),
            'message' => __('messages.delete_confirm_text'),
            'confirmText' => __('buttons.delete'),
            'cancelText' => __('buttons.cancel'),
            'confirmVariant' => 'danger',
            'cancelVariant' => 'outline',
            'data' => $data,
        ]);
    }

    /**
     * Show a custom confirmation modal.
     */
    public function confirmAction(string $title, string $message, string $action, array $data = []): void
    {
        $this->showConfirmation($action, [], [
            'title' => $title,
            'message' => $message,
            'confirmText' => __('buttons.confirm'),
            'cancelText' => __('buttons.cancel'),
            'confirmVariant' => 'primary',
            'cancelVariant' => 'outline',
            'data' => $data,
        ]);
    }
}
