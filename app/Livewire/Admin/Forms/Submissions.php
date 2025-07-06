<?php

namespace App\Livewire\Admin\Forms;

use App\Models\Form;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
class Submissions extends Component
{
    use WithPagination;

    public Form $form;

    public string $search = '';

    public int $perPage = 10;

    public string $sortBy = 'created_at';

    public string $sortDirection = 'desc';

    /**
     * The querystring properties.
     *
     * @var array
     */
    protected $queryString = [
        'search' => ['except' => ''],
        'perPage' => ['except' => 10],
        'sortBy' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
    ];

    public function mount(Form $form)
    {
        $this->form = $form;
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedPerPage(): void
    {
        $this->resetPage();
    }

    public function sort(string $column): void
    {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'asc';
        }
    }

    public function render()
    {
        $submissions = $this->form->submissions()
            ->when($this->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('ip_address', 'like', '%'.$search.'%')
                        ->orWhere('user_agent', 'like', '%'.$search.'%')
                        ->orWhereRaw("JSON_EXTRACT(data, '$.*') LIKE ?", ["%{$search}%"]);
                });
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);

        return view('livewire.admin.forms.submissions', [
            'submissions' => $submissions,
        ])->title(__('forms.submissions_for', ['name' => $this->form->getTranslation('name', app()->getLocale())]));
    }
}
