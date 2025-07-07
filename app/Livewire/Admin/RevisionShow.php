<?php

declare(strict_types=1);

namespace App\Livewire\Admin;

use App\Models\ContentBlock;
use App\Models\Form;
use App\Models\Page;
use Illuminate\Database\Eloquent\Model;
use Livewire\Component;

/**
 * Livewire component for showing revision history for any model.
 */
class RevisionShow extends Component
{
    /**
     * The type of model (form, page, content-block).
     */
    public string $modelType;

    /**
     * The ID of the model.
     */
    public int $modelId;

    /**
     * The model instance.
     */
    public ?Model $model = null;

    /**
     * Mount the component.
     */
    public function mount(string $modelType, int $modelId): void
    {
        $this->modelType = $modelType;
        $this->modelId = $modelId;
        $this->model = $this->getModel($modelType, $modelId);

        if (! $this->model) {
            abort(404);
        }
    }

    /**
     * Get the model instance based on type and ID.
     *
     * @param  string  $modelType  The type of model
     * @param  int  $modelId  The ID of the model
     * @return mixed The model instance or null if not found
     */
    protected function getModel(string $modelType, int $modelId)
    {
        return match ($modelType) {
            'form' => Form::find($modelId),
            'page' => Page::find($modelId),
            'content-block' => ContentBlock::find($modelId),
            default => null,
        };
    }

    /**
     * Render the component.
     */
    public function render()
    {
        return view('livewire.admin.revision-show')
            ->layout('components.layouts.app');
    }
}
