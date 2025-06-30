<?php

namespace App\Livewire\ResourceSystem;

use App\Services\ResourceSystem\Resource;
use App\Traits\WithToastNotifications;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Schema;
use Livewire\Component;
use Livewire\WithPagination;

class ResourceTable extends Component
{
    use WithPagination, WithToastNotifications;

    /**
     * The resource class.
     *
     * @var string
     */
    public $resource;

    /**
     * The search query.
     *
     * @var string
     */
    public $search = '';

    /**
     * The column to sort by.
     *
     * @var string|null
     */
    public $sortBy;

    /**
     * The direction to sort.
     *
     * @var string
     */
    public $sortDirection = 'asc';

    /**
     * The filters.
     *
     * @var array
     */
    public $filters = [];

    /**
     * The number of items per page.
     *
     * @var int
     */
    public $perPage = 10;

    /**
     * Whether to show the filter popover.
     */
    public bool $showFiltersPopover = false;

    /**
     * Whether to show the delete confirmation modal.
     *
     * @var bool
     */
    public $showDeleteModal = false;

    /**
     * The ID of the resource to delete.
     *
     * @var int|null
     */
    public $deleteId;

    /**
     * Whether reordering is enabled.
     */
    public bool $reorderingEnabled = false;

    /**
     * The querystring properties.
     *
     * @var array
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
        $this->resource = $resource::class;
        $modelTable = $this->getResourceInstance()::$model::make()->getTable();

        if (Schema::hasColumn($modelTable, 'order')) {
            $this->sortBy = 'order';
            $this->reorderingEnabled = true;
        }
    }

    /**
     * Get the resource instance.
     *
     * @return resource
     */
    public function getResourceInstance()
    {
        return $this->resource::make();
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
    }

    /**
     * Reset the filters.
     */
    public function resetFilters(): void
    {
        $this->filters = [];
        $this->showFiltersPopover = false;
    }

    /**
     * Reset the pagination.
     */
    public function resetPage(): void
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
        $model = $this->resource::$model;
        $model::findOrFail($this->deleteId)->delete();

        $this->showDeleteModal = false;
        $this->deleteId = null;

        $this->showSuccessToast(
            __('messages.resource.deleted', ['Resource' => $this->getResourceInstance()::singularLabel()]),
            __('messages.success.generic')
        );
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
        $modelClass = $this->resource::$model;

        if (in_array(\Spatie\EloquentSortable\SortableTrait::class, class_uses_recursive($modelClass))) {
            $modelClass::setNewOrder($order);
            $this->showSuccessToast(__('messages.success.generic'));
        }
    }

    /**
     * Build the query for the resource.
     */
    public function buildQuery(array $columns, array $filters): Builder
    {
        $this->getResourceInstance();
        $query = $this->resource::newQuery();

        // Apply search
        $searchableColumns = collect($columns)
            ->filter(fn ($column) => $column->isSearchable())
            ->map(fn ($column) => $column->getName())
            ->toArray();

        if ($this->search && count($searchableColumns) > 0) {
            $query->where(function (Builder $query) use ($searchableColumns): void {
                foreach ($searchableColumns as $column) {
                    $query->orWhere($column, 'like', '%'.$this->search.'%');
                }
            });
        }

        // Apply sorting
        if ($this->sortBy) {
            $column = collect($columns)->first(fn ($column): bool => $column->getName() === $this->sortBy);

            if ($column && $column->isSortable()) {
                $query = $column->applySort($query, $this->sortDirection);
            }
        } else {
            // Default sorting if no column is specified
            $model = $this->getResourceInstance()::$model::make();
            if (Schema::hasColumn($model->getTable(), 'order')) {
                $query->orderBy('order');
            }
        }

        // Apply filters
        foreach ($this->filters as $name => $value) {
            $filter = collect($filters)->first(fn ($filter): bool => $filter->getName() === $name);

            if ($filter) {
                $query = $filter->apply($query, $value);
            }
        }

        return $query;
    }

    /**
     * Get the resources.
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getResources(array $columns, array $filters)
    {
        $query = $this->buildQuery($columns, $filters);

        // Check if the model has an 'order' column. If so, don't paginate.
        // This is a simple way to handle reorderable resources.
        $model = $this->resource::newModel();
        if (in_array('order', $model->getFillable()) || in_array('order', $model->getGuarded()) === false && Schema::hasColumn($model->getTable(), 'order')) {
            return $query->get();
        }

        return $query->paginate($this->perPage);
    }

    /**
     * Render the component.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        $resourceInstance = $this->getResourceInstance();
        $columns = $resourceInstance->columns();
        $filters = $resourceInstance->filters();

        $orderColumn = null;
        $orderColumnIndex = -1;
        $handleColumnIndex = -1;

        foreach ($columns as $index => $column) {
            if ($column->getName() === 'order') {
                $orderColumn = $column;
                $orderColumnIndex = $index;
            }
            if ($column->getName() === 'handle') {
                $handleColumnIndex = $index;
            }
        }

        if ($orderColumn && $handleColumnIndex !== -1 && $orderColumnIndex !== -1) {
            unset($columns[$orderColumnIndex]);
            $columns = array_values($columns);
            $handleColumnIndex = array_search('handle', array_map(fn ($col) => $col->getName(), $columns));
            if ($handleColumnIndex !== false) {
                array_splice($columns, $handleColumnIndex + 1, 0, [$orderColumn]);
            }
        }

        return view('livewire.resource-system.resource-table', [
            'resources' => $this->getResources($columns, $filters),
            'columns' => $columns,
            'availableFilters' => $filters,
        ]);
    }
}
