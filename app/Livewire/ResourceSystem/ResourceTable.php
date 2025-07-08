<?php

declare(strict_types=1);

namespace App\Livewire\ResourceSystem;

use App\Services\ResourceSystem\Resource;
use App\Traits\WithToastNotifications;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Schema;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Livewire component for displaying resource tables.
 *
 * This component provides a comprehensive table view for resources with
 * search, sorting, filtering, and pagination capabilities. It uses
 * services for business logic and DTOs for data handling.
 */
class ResourceTable extends Component
{
    use WithPagination, WithToastNotifications;

    /**
     * The resource class.
     */
    public ?string $resourceClass = null;

    /**
     * The resource instance.
     */
    protected ?Resource $resourceInstance = null;

    /**
     * The search query.
     */
    public string $search = '';

    /**
     * The column to sort by.
     */
    public ?string $sortBy = null;

    /**
     * The direction to sort.
     */
    public string $sortDirection = 'asc';

    /**
     * The filters.
     *
     * @var array<string, mixed>
     */
    public array $filters = [];

    /**
     * The number of items per page.
     */
    public int $perPage = 10;

    /**
     * Whether to show the filter popover.
     */
    public bool $showFiltersPopover = false;

    /**
     * Whether to show the delete confirmation modal.
     */
    public bool $showDeleteModal = false;

    /**
     * The ID of the resource to delete.
     */
    public ?int $deleteId = null;

    /**
     * Whether reordering is enabled.
     */
    public bool $reorderingEnabled = false;

    /**
     * The querystring properties.
     *
     * @var array<string, array<string, mixed>>
     */
    protected $queryString = [
        'search' => ['except' => ''],
        'sortBy' => ['except' => 'order'],
        'sortDirection' => ['except' => 'asc'],
        'filters' => ['except' => []],
        'perPage' => ['except' => 10],
    ];

    /**
     * Mount the component.
     */
    public function mount(Resource $resource): void
    {
        $this->resourceInstance = $resource;
        $this->resourceClass = $resource::class;
        
        // Ensure the resource instance is properly set before proceeding
        if ($this->resourceInstance === null) {
            throw new \RuntimeException('Failed to initialize resource instance during mount.');
        }
        
        $modelTable = $this->getResourceInstance()::$model::make()->getTable();

        if (Schema::hasColumn($modelTable, 'order')) {
            $this->sortBy = 'order';
            $this->reorderingEnabled = true;
        }
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
     * Sort by the given column.
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
     * Reset the search.
     */
    public function resetSearch(): void
    {
        $this->search = '';
        $this->resetPage();
    }

    /**
     * Reset the filters.
     */
    public function resetFilters(): void
    {
        $this->filters = [];
        $this->showFiltersPopover = false;
        $this->resetPage();
    }

    /**
     * Reset the pagination.
     */
    public function resetPagination(): void
    {
        $this->resetPage();
    }

    /**
     * Confirm the deletion of the resource.
     */
    public function confirmDelete(int $id): void
    {
        $this->deleteId = $id;
        $this->showDeleteModal = true;
    }

    /**
     * Delete the resource.
     */
    public function delete(): void
    {
        try {
            $model = $this->resourceClass::$model;
            $resource = $model::findOrFail($this->deleteId);
            
            // Use the resource's delete method if available
            $resourceInstance = $this->getResourceInstance();
            if (method_exists($resourceInstance, 'deleteResource')) {
                $resourceInstance->deleteResource($resource);
            } else {
                $resource->delete();
            }

            $this->showDeleteModal = false;
            $this->deleteId = null;

            $this->showSuccessToast(
                __('messages.resource.deleted', ['Resource' => $this->getResourceInstance()::singularLabel()])
            );

        } catch (\Exception $e) {
            logger()->error('Failed to delete resource', [
                'resource_id' => $this->deleteId,
                'resource_class' => $this->resourceClass,
                'error' => $e->getMessage(),
            ]);

            $this->showErrorToast(__('messages.resource.delete_failed'));
        }
    }

    /**
     * Cancel the deletion of the resource.
     */
    public function cancelDelete(): void
    {
        $this->showDeleteModal = false;
        $this->deleteId = null;
    }

    /**
     * Reorder the resources.
     */
    public function reorder(array $order): void
    {
        try {
            $modelClass = $this->resourceClass::$model;

            if (in_array(\Spatie\EloquentSortable\SortableTrait::class, class_uses_recursive($modelClass))) {
                $modelClass::setNewOrder($order);
                
                $this->showSuccessToast(__('messages.resource.reordered'));
            }
        } catch (\Exception $e) {
            logger()->error('Failed to reorder resources', [
                'resource_class' => $this->resourceClass,
                'order' => $order,
                'error' => $e->getMessage(),
            ]);

            $this->showErrorToast(__('messages.resource.reorder_failed'));
        }
    }

    /**
     * Update filters.
     */
    public function updatedFilters(): void
    {
        $this->resetPage();
    }

    /**
     * Update search.
     */
    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    /**
     * Update per page.
     */
    public function updatedPerPage(): void
    {
        $this->resetPage();
    }

    /**
     * Build the query for resources.
     */
    public function buildQuery(array $columns, array $filters): Builder
    {
        $modelClass = $this->resourceClass::$model;
        $query = $modelClass::query();

        // Apply search
        if ($this->search) {
            $searchableColumns = $this->getResourceInstance()->searchableColumns();
            $query->where(function ($q) use ($searchableColumns) {
                foreach ($searchableColumns as $column) {
                    $q->orWhere($column, 'like', '%'.$this->search.'%');
                }
            });
        }

        // Apply filters
        foreach ($filters as $filter) {
            $filterName = $filter->getName();
            $filterValue = $this->filters[$filterName] ?? null;
            
            if ($filterValue !== null && $filterValue !== '') {
                $query = $filter->apply($query, $filterValue);
            }
        }

        // Apply sorting
        if ($this->sortBy) {
            $query->orderBy($this->sortBy, $this->sortDirection);
        }

        return $query;
    }

    /**
     * Get resources with pagination.
     */
    public function getResources(array $columns, array $filters)
    {
        $query = $this->buildQuery($columns, $filters);
        
        return $query->paginate($this->perPage);
    }

    /**
     * Get available filters for the resource.
     */
    public function getAvailableFilters(): array
    {
        return $this->getResourceInstance()->filters();
    }

    /**
     * Get sortable columns for the resource.
     */
    public function getSortableColumns(): array
    {
        return $this->getResourceInstance()->sortableColumns();
    }

    /**
     * Render the component.
     */
    public function render()
    {
        $resourceInstance = $this->getResourceInstance();
        $columns = $resourceInstance->columns();
        $availableFilters = $this->getAvailableFilters();

        $resources = $this->getResources($columns, $availableFilters);

        return view('livewire.resource-system.resource-table', [
            'resources' => $resources,
            'columns' => $columns,
            'availableFilters' => $availableFilters,
            'resourceInstance' => $resourceInstance,
        ])->title($resourceInstance::pluralLabel());
    }
}
