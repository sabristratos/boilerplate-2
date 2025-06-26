<?php

namespace App\Livewire\ResourceSystem;

use App\Services\ResourceSystem\Resource;
use App\Traits\WithToastNotifications;
use Flux\Flux;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Schema;

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
    public $sortBy = null;

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
     * Whether to show the filter modal.
     *
     * @var bool
     */
    public bool $showFiltersModal = false;

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
    public $deleteId = null;

    /**
     * Whether reordering is enabled.
     *
     * @var bool
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
     *
     * @param  Resource  $resource
     * @return void
     */
    public function mount(Resource $resource)
    {
        $this->resource = get_class($resource);
        $modelTable = $this->getResourceInstance()::$model::make()->getTable();

        if (Schema::hasColumn($modelTable, 'order')) {
            $this->sortBy = 'order';
            $this->reorderingEnabled = true;
        }
    }

    /**
     * Get the resource instance.
     *
     * @return Resource
     */
    public function getResourceInstance()
    {
        return $this->resource::make();
    }

    /**
     * Sort by the given column.
     *
     * @param  string  $column
     * @return void
     */
    public function sort(string $column)
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
     *
     * @return void
     */
    public function resetSearch()
    {
        $this->search = '';
    }

    /**
     * Reset the filters.
     *
     * @return void
     */
    public function resetFilters()
    {
        $this->filters = [];
        $this->showFiltersModal = false;
    }

    /**
     * Reset the pagination.
     *
     * @return void
     */
    public function resetPage()
    {
        $this->resetPage();
    }

    /**
     * Confirm the deletion of the resource.
     *
     * @param  int  $id
     * @return void
     */
    public function confirmDelete(int $id)
    {
        $this->deleteId = $id;
        $this->showDeleteModal = true;
    }

    /**
     * Delete the resource.
     *
     * @return void
     */
    public function delete()
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
     *
     * @return void
     */
    public function cancelDelete()
    {
        $this->showDeleteModal = false;
        $this->deleteId = null;
    }

    /**
     * Reorder the resources.
     *
     * @param  array  $order
     * @return void
     */
    public function reorder(array $order)
    {
        $modelClass = $this->resource::$model;

        if (in_array(\Spatie\EloquentSortable\SortableTrait::class, class_uses_recursive($modelClass))) {
            $modelClass::setNewOrder($order);
            $this->showSuccessToast(__("messages.success.generic"));
        }
    }

    /**
     * Build the query for the resource.
     *
     * @return Builder
     */
    public function buildQuery(array $columns, array $filters): Builder
    {
        $resource = $this->getResourceInstance();
        $query = $this->resource::newQuery();

        // Apply search
        $searchableColumns = collect($columns)
            ->filter(fn ($column) => $column->isSearchable())
            ->map(fn ($column) => $column->getName())
            ->toArray();

        if ($this->search && count($searchableColumns) > 0) {
            $query->where(function (Builder $query) use ($searchableColumns) {
                foreach ($searchableColumns as $column) {
                    $query->orWhere($column, 'like', '%' . $this->search . '%');
                }
            });
        }

        // Apply sorting
        if ($this->sortBy) {
            $column = collect($columns)->first(function ($column) {
                return $column->getName() === $this->sortBy;
            });

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
            $filter = collect($filters)->first(function ($filter) use ($name) {
                return $filter->getName() === $name;
            });

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
            $handleColumnIndex = array_search('handle', array_map(fn($col) => $col->getName(), $columns));
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
