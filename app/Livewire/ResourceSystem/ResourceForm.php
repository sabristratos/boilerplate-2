<?php

namespace App\Livewire\ResourceSystem;

use App\Services\ResourceSystem\Resource;
use App\Traits\WithToastNotifications;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Component;

class ResourceForm extends Component
{
    use WithToastNotifications;

    /**
     * The resource class.
     *
     * @var string
     */
    public $resource;

    /**
     * The resource ID.
     *
     * @var int|null
     */
    public $resourceId;

    /**
     * The form data.
     *
     * @var array
     */
    public $data = [];

    /**
     * Mount the component.
     */
    public function mount(Resource $resource, ?int $resourceId = null): void
    {
        $this->resource = $resource::class;
        $this->resourceId = $resourceId;

        $this->loadData();
    }

    /**
     * Get the resource instance.
     *
     * @return \App\Services\ResourceSystem\Resource
     */
    public function getResourceInstance()
    {
        return $this->resource::make();
    }

    /**
     * Get the model instance.
     */
    public function getModelInstance(): ?Model
    {
        if (! $this->resourceId) {
            return $this->resource::newModel();
        }

        $model = $this->resource::$model;

        return $model::findOrFail($this->resourceId);
    }

    /**
     * Load the form data.
     */
    public function loadData(): void
    {
        $model = $this->getModelInstance();
        $fields = $this->getResourceInstance()->fields();
        $hasRevisions = method_exists($model, 'latestRevision');

        foreach ($fields as $field) {
            $name = $field->getName();
            if ($model->exists) {
                if ($name === 'roles') {
                    $value = $model->getRoleNames()->toArray();
                } elseif ($hasRevisions) {
                    // Load from latest revision if available
                    $latestRevision = $model->latestRevision();
                    if ($latestRevision && isset($latestRevision->data[$name])) {
                        $value = $latestRevision->data[$name];
                    } else {
                        $value = $model->{$name};
                    }
                } else {
                    $value = $model->{$name};
                }
            } else {
                $value = $field->getDefaultValue();
            }

            $this->data[$name] = $value;
        }
    }

    /**
     * Publish the current draft revision.
     */
    public function publish(): void
    {
        $model = $this->getModelInstance();
        $hasRevisions = method_exists($model, 'createRevision');

        if (! $hasRevisions || ! $model->exists) {
            $this->showErrorToast(
                __('messages.errors.generic'),
                __('messages.errors.generic')
            );

            return;
        }

        DB::beginTransaction();

        try {
            // Create a published revision with the current form data
            $data = collect($this->data)->except(['avatar', 'roles']);
            $model->createManualRevision('publish', 'Resource published', $data->toArray(), true);

            DB::commit();

            $this->showSuccessToast(
                __('messages.resource.published', ['Resource' => $this->getResourceInstance()::singularLabel()]),
                __('messages.success.generic')
            );

            // Redirect to resource index after success
            $this->redirectRoute('admin.'.$this->getResourceInstance()::uriKey().'.index', navigate: true);

        } catch (\Exception $e) {
            DB::rollBack();
            $this->showErrorToast(
                $e->getMessage(),
                __('messages.errors.generic')
            );
        }
    }

    /**
     * Check if the model supports revisions.
     */
    #[Computed]
    public function supportsRevisions(): bool
    {
        $model = $this->getModelInstance();

        return method_exists($model, 'createRevision');
    }

    /**
     * Check if the model has unsaved changes (draft revisions).
     */
    #[Computed]
    public function hasUnsavedChanges(): bool
    {
        $model = $this->getModelInstance();

        if (! $this->supportsRevisions() || ! $model->exists) {
            return false;
        }

        $latestRevision = $model->latestRevision();
        if (! $latestRevision) {
            return false;
        }

        // Compare current form data with the latest revision
        $currentData = collect($this->data)->except(['avatar', 'roles']);
        $revisionData = $latestRevision->data;

        return $currentData->diffAssoc($revisionData)->isNotEmpty() ||
               collect($revisionData)->diffAssoc($currentData)->isNotEmpty();
    }

    /**
     * Get the validation rules.
     */
    public function rules(): array
    {
        $rules = [];
        $fields = $this->getResourceInstance()->fields();

        foreach ($fields as $field) {
            $rules['data.'.$field->getName()] = $field->getRules();
        }

        return $rules;
    }

    /**
     * Get the validation attributes.
     */
    public function validationAttributes(): array
    {
        $attributes = [];
        $fields = $this->getResourceInstance()->fields();

        foreach ($fields as $field) {
            $attributes['data.'.$field->getName()] = $field->getLabel();
        }

        return $attributes;
    }

    /**
     * Save the resource.
     */
    public function save(): void
    {
        if (isset($this->data['roles']) && ! is_array($this->data['roles'])) {
            $this->data['roles'] = [];
        }

        $this->validate();

        DB::beginTransaction();

        try {
            $model = $this->getModelInstance();
            $isNew = ! $model->exists;
            $hasRevisions = method_exists($model, 'createRevision');

            $data = collect($this->data)->except(['avatar', 'roles']);

            // Only update password if it's not empty
            if (empty($data['password'])) {
                unset($data['password']);
            }

            if ($hasRevisions) {
                // Use revision system for models that support it
                if ($isNew) {
                    // For new models, save first to get an ID, then create a published revision
                    foreach ($data as $key => $value) {
                        $model->{$key} = $value;
                    }
                    $model->save();

                    // Update the resourceId so subsequent calls work correctly
                    $this->resourceId = $model->id;

                    // Create initial published revision
                    $model->createManualRevision('create', 'Initial creation', $data->toArray(), true);
                } else {
                    // For existing models, create a draft revision
                    $model->createManualRevision('update', 'Resource updated', $data->toArray(), false);
                }
            } else {
                // Fallback to direct model updates for non-revision models
                foreach ($data as $key => $value) {
                    $model->{$key} = $value;
                }
                $model->save();
            }

            if (isset($this->data['roles'])) {
                $model->syncRoles($this->data['roles']);
            }

            // Handle media reattachment after model is saved
            $this->handleMediaReattachment($model);

            DB::commit();

            // Clear password field from data after successful save
            if (isset($this->data['password'])) {
                $this->data['password'] = '';
            }

            $this->showSuccessToast(
                $isNew
                    ? __('messages.resource.created', ['Resource' => $this->getResourceInstance()::singularLabel()])
                    : __('messages.resource.updated', ['Resource' => $this->getResourceInstance()::singularLabel()]),
                __('messages.success.generic')
            );

            // Redirect to resource index after success
            $this->redirectRoute('admin.'.$this->getResourceInstance()::uriKey().'.index', navigate: true);

        } catch (\Exception $e) {
            DB::rollBack();
            $this->showErrorToast(
                $e->getMessage(),
                __('messages.errors.generic')
            );
        }
    }

    /**
     * Handle media reattachment after model is saved.
     */
    protected function handleMediaReattachment($model): void
    {
        // Get all temporary media for this session and model type
        $sessionId = session()->getId();
        $modelType = get_class($model);

        $temporaryMediaRecords = \App\Models\TemporaryMedia::where('session_id', $sessionId)
            ->where('model_type', $modelType)
            ->get();

        foreach ($temporaryMediaRecords as $tempMedia) {
            $media = $tempMedia->getFirstMedia('temp');

            if ($media) {
                // Copy the media to the actual model
                $media->copy($model, $tempMedia->collection_name);

                // Delete the temporary media record and its media
                $tempMedia->delete();
            }
        }
    }

    /**
     * Render the component.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('livewire.resource-system.resource-form', [
            'resource' => $this->getResourceInstance(),
            'model' => $this->getModelInstance(),
            'fields' => $this->getResourceInstance()->fields(),
        ]);
    }
}
