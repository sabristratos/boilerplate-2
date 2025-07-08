<?php

declare(strict_types=1);

namespace App\Livewire\ResourceSystem;

use App\Services\ResourceSystem\Resource;
use App\Traits\WithToastNotifications;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Component;

/**
 * Livewire component for resource forms.
 *
 * This component provides form functionality for creating and editing
 * resources with support for validation, media handling, and revisions.
 * It uses services for business logic and DTOs for data handling.
 */
class ResourceForm extends Component
{
    use WithToastNotifications;

    /**
     * The resource class.
     */
    public string $resourceClass;

    /**
     * The resource instance.
     */
    protected ?Resource $resourceInstance = null;

    /**
     * The resource ID.
     */
    public ?int $resourceId = null;

    /**
     * The form data.
     *
     * @var array<string, mixed>
     */
    public array $data = [];

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        // The resource and resourceId are passed as public properties from the view
        if (empty($this->resourceClass)) {
            throw new \RuntimeException('Resource class not provided to component.');
        }

        // Create the resource instance if not already set
        if ($this->resourceInstance === null) {
            $this->resourceInstance = new $this->resourceClass;
        }

        // Clear any existing temporary media for new resource creation
        if (!$this->resourceId) {
            $this->clearTemporaryMedia();
        }

        $this->loadData();
    }

    /**
     * Get the resource instance.
     */
    public function getResourceInstance(): Resource
    {
        if ($this->resourceInstance === null) {
            // Try to initialize the resource instance if it's not set
            if (!empty($this->resourceClass)) {
                $this->resourceInstance = new $this->resourceClass;
            } else {
                throw new \RuntimeException('Resource instance not initialized. Make sure the component is properly mounted.');
            }
        }
        
        return $this->resourceInstance;
    }

    /**
     * Get the model instance.
     */
    public function getModelInstance(): ?Model
    {
        if (empty($this->resourceClass)) {
            throw new \RuntimeException('Resource class not initialized. Make sure the component is properly mounted.');
        }

        if (!$this->resourceId) {
            $model = $this->resourceClass::$model;
            return new $model();
        }

        $model = $this->resourceClass::$model;

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
                    $value = $latestRevision && isset($latestRevision->data[$name]) ? $latestRevision->data[$name] : $model->{$name};
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

        if (!$hasRevisions || !$model->exists) {
            $this->showErrorToast(__('messages.errors.generic'));
            return;
        }

        DB::beginTransaction();

        try {
            // Create a published revision with the current form data
            $data = collect($this->data)->except(['avatar', 'roles']);
            $model->createManualRevision('publish', 'Resource published', $data->toArray(), true);

            DB::commit();

            $this->showSuccessToast(
                __('messages.resource.published', ['Resource' => $this->getResourceInstance()::singularLabel()])
            );

            // Redirect to resource index after success
            $this->redirectRoute('admin.resources.'.$this->getResourceInstance()::uriKey().'.index', navigate: true);

        } catch (\Exception $e) {
            DB::rollBack();
            
            logger()->error('Failed to publish resource', [
                'resource_id' => $this->resourceId,
                'resource_class' => $this->resource,
                'error' => $e->getMessage(),
            ]);

            $this->showErrorToast($e->getMessage());
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

        if (!$this->supportsRevisions() || !$model->exists) {
            return false;
        }

        $latestRevision = $model->latestRevision();
        if (!$latestRevision) {
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
     *
     * @return array<string, array<string>>
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
     *
     * @return array<string, string>
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
        $this->validate();

        DB::beginTransaction();

        try {
            $model = $this->getModelInstance();
            $fields = $this->getResourceInstance()->fields();
            $hasRevisions = method_exists($model, 'createRevision');

            // Prepare data for saving
            $saveData = [];
            foreach ($fields as $field) {
                $name = $field->getName();
                
                // Skip special fields that are handled separately
                if (in_array($name, ['avatar', 'roles'])) {
                    continue;
                }

                $value = $this->data[$name] ?? null;
                
                // Apply field transformations
                if (method_exists($field, 'transformValue')) {
                    $value = $field->transformValue($value);
                }

                $saveData[$name] = $value;
            }

            // Save the model
            if ($model->exists) {
                $model->update($saveData);
            } else {
                $model->fill($saveData);
                $model->save();
            }

            // Handle special fields
            $this->handleSpecialFields($model);

            // Handle media reattachment if needed
            $this->handleMediaReattachment($model);

            // Create revision if supported
            if ($hasRevisions && $model->exists) {
                $revisionData = collect($this->data)->except(['avatar', 'roles']);
                $model->createManualRevision('update', 'Resource updated', $revisionData->toArray(), false);
            }

            DB::commit();

            $this->showSuccessToast(
                __('messages.resource.saved', ['Resource' => $this->getResourceInstance()::singularLabel()])
            );

            // Redirect to resource index after success
            $this->redirectRoute('admin.resources.'.$this->getResourceInstance()::uriKey().'.index', navigate: true);

        } catch (\Exception $e) {
            DB::rollBack();
            
            logger()->error('Failed to save resource', [
                'resource_id' => $this->resourceId,
                'resource_class' => $this->resourceClass,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            $this->showErrorToast($e->getMessage());
        }
    }

    /**
     * Handle special fields like roles.
     */
    protected function handleSpecialFields(Model $model): void
    {
        // Handle roles
        if (isset($this->data['roles']) && method_exists($model, 'syncRoles')) {
            $model->syncRoles($this->data['roles']);
        }
    }

    /**
     * Clear temporary media for all media fields in the resource.
     */
    protected function clearTemporaryMedia(): void
    {
        try {
            $fields = $this->getResourceInstance()->fields();
            $sessionId = session()->getId();
            
            foreach ($fields as $field) {
                if ($field instanceof \App\Services\ResourceSystem\Fields\Media) {
                    $fieldName = $field->getName();
                    \App\Models\TemporaryMedia::clearForSession($sessionId, $fieldName);
                }
            }
        } catch (\Exception $e) {
            logger()->error('Failed to clear temporary media', [
                'error' => $e->getMessage(),
                'session_id' => session()->getId(),
                'resource_class' => $this->resourceClass,
            ]);
        }
    }

    /**
     * Handle media reattachment for temporary media.
     */
    protected function handleMediaReattachment(Model $model): void
    {
        // Handle avatar/media fields that might have temporary media
        if (method_exists($model, 'getFirstMedia')) {
            $fields = $this->getResourceInstance()->fields();
            
            foreach ($fields as $field) {
                if ($field instanceof \App\Services\ResourceSystem\Fields\Media) {
                    $fieldName = $field->getName();
                    
                    // Check if there's temporary media to reattach
                    $temporaryMedia = \App\Models\TemporaryMedia::getForSession(
                        session()->getId(),
                        $fieldName
                    );

                    if ($temporaryMedia && $temporaryMedia->getFirstMedia('temp')) {
                        // Clear existing media and copy from temporary
                        $model->clearMediaCollection($fieldName);
                        $temporaryMedia->getFirstMedia('temp')->copy($model, $fieldName);
                        
                        // Clean up temporary media
                        $temporaryMedia->delete();
                    }
                }
            }
        }
    }

    /**
     * Get form fields for the resource.
     */
    public function getFormFields(): array
    {
        return $this->getResourceInstance()->fields();
    }

    /**
     * Get resource title for the page.
     */
    public function getResourceTitle(): string
    {
        $resourceInstance = $this->getResourceInstance();
        
        if ($this->resourceId) {
            return __('messages.resource.edit_title', [
                'Resource' => $resourceInstance::singularLabel()
            ]);
        }

        return __('messages.resource.create_title', [
            'Resource' => $resourceInstance::singularLabel()
        ]);
    }

    /**
     * Render the component.
     */
    public function render()
    {
        $resourceInstance = $this->getResourceInstance();
        $fields = $resourceInstance->fields();
        $model = $this->getModelInstance();

        return view('livewire.resource-system.resource-form', [
            'fields' => $fields,
            'model' => $model,
            'resourceInstance' => $resourceInstance,
        ])->title($this->getResourceTitle());
    }
}
