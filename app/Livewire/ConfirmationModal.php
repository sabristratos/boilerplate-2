<?php

namespace App\Livewire;

use Livewire\Attributes\On;
use Livewire\Component;

class ConfirmationModal extends Component
{
    public bool $show = false;

    public string $title = '';

    public string $message = '';

    public string $confirmText = 'Confirm';

    public string $cancelText = 'Cancel';

    public string $confirmVariant = 'primary';

    public string $cancelVariant = 'outline';

    public ?string $action = null;

    public array $actionParams = [];

    public array $data = [];

    public array $params = [];

    #[On('show-confirmation')]
    public function open(array $data): void
    {
        $this->action = $data['action'];
        $this->actionParams = $data['actionParams'];
        $this->title = $data['title'] ?? 'Are you sure?';
        $this->message = $data['message'] ?? 'This action cannot be undone.';
        $this->confirmText = $data['confirmText'] ?? 'Confirm';
        $this->cancelText = $data['cancelText'] ?? 'Cancel';
        $this->confirmVariant = $data['confirmVariant'] ?? 'primary';
        $this->cancelVariant = $data['cancelVariant'] ?? 'outline';
        $this->data = $data['data'] ?? [];
        $this->show = true;
    }

    #[On('close-confirmation-modal')]
    public function close(): void
    {
        $this->resetModal();
    }

    public function confirm(): void
    {
        if ($this->action !== null && $this->action !== '' && $this->action !== '0') {
            $this->dispatch($this->action, ...array_merge($this->actionParams, ['data' => $this->data]));
        }

        $this->resetModal();
    }

    public function updatedShow(bool $value): void
    {
        if (! $value) {
            $this->resetModal();
        }
    }

    public function resetModal(): void
    {
        $this->reset();
    }

    public function render()
    {
        return view('livewire.confirmation-modal');
    }
}
