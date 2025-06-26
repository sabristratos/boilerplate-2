<?php

namespace App\Livewire\ResourceSystem;

use App\Services\ResourceSystem\Resource;
use App\Traits\WithToastNotifications;
use Livewire\Component;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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
     *
     * @param  \App\Services\ResourceSystem\Resource  $resource
     * @param  int|null  $resourceId
     * @return void
     */
    public function mount(Resource $resource, ?int $resourceId = null)
    {
        $this->resource = get_class($resource);
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
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function getModelInstance(): ?Model
    {
        if (!$this->resourceId) {
            return $this->resource::newModel();
        }

        $model = $this->resource::$model;

        return $model::findOrFail($this->resourceId);
    }

    /**
     * Load the form data.
     *
     * @return void
     */
    public function loadData()
    {
        $model = $this->getModelInstance();
        $fields = $this->getResourceInstance()->fields();

        foreach ($fields as $field) {
            $name = $field->getName();
            if ($model->exists) {
                if ($name === 'roles') {
                    $value = $model->getRoleNames()->toArray();
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
     * Get the validation rules.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [];
        $fields = $this->getResourceInstance()->fields();

        foreach ($fields as $field) {
            $rules['data.' . $field->getName()] = $field->getRules();
        }

        return $rules;
    }

    /**
     * Get the validation attributes.
     *
     * @return array
     */
    public function validationAttributes()
    {
        $attributes = [];
        $fields = $this->getResourceInstance()->fields();

        foreach ($fields as $field) {
            $attributes['data.' . $field->getName()] = $field->getLabel();
        }

        return $attributes;
    }

    /**
     * Save the resource.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function save()
    {
        if (isset($this->data['roles']) && !is_array($this->data['roles'])) {
            $this->data['roles'] = [];
        }

        $this->validate();

        DB::beginTransaction();

        try {
            $model = $this->getModelInstance();
            $isNew = !$model->exists;

            $data = collect($this->data)->except(['avatar', 'roles']);

            // Only update password if it's not empty
            if (empty($data['password'])) {
                unset($data['password']);
            }

            foreach ($data as $key => $value) {
                $model->{$key} = $value;
            }

            $model->save();

            if (isset($this->data['roles'])) {
                $model->syncRoles($this->data['roles']);
            }

            DB::commit();

            if ($isNew) {
                $this->resourceId = $model->id;
            }

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

        } catch (\Exception $e) {
            DB::rollBack();
            $this->showErrorToast(
                $e->getMessage(),
                __('messages.errors.generic')
            );
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
