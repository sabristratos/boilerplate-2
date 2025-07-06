<?php

namespace App\Livewire;

use App\Models\Page;
use Livewire\Component;
use Illuminate\Support\Collection;

class SidebarSearch extends Component
{
    public string $search = '';
    public bool $showResults = false;
    public Collection $searchResults;

    public function mount()
    {
        $this->searchResults = collect();
    }

    public function updatedSearch()
    {
        if (strlen($this->search) < 2) {
            $this->searchResults = collect();
            $this->showResults = false;
            return;
        }

        $this->searchResults = $this->performSearch();
        $this->showResults = $this->searchResults->isNotEmpty();
    }

    public function performSearch(): Collection
    {
        $searchTerm = strtolower($this->search);
        $results = collect();

        // Navigation items
        $navigationItems = $this->getNavigationItems();
        foreach ($navigationItems as $item) {
            if (str_contains(strtolower($item['label']), $searchTerm) ||
                str_contains(strtolower($item['description'] ?? ''), $searchTerm)) {
                $results->push($item);
            }
        }

        // Pages
        $pages = Page::where('title', 'like', "%{$this->search}%")
            ->orWhere('slug', 'like', "%{$this->search}%")
            ->limit(5)
            ->get();

        foreach ($pages as $page) {
            $results->push([
                'type' => 'page',
                'label' => $page->title,
                'description' => __('navigation.view_page'),
                'url' => route('pages.show', $page->slug),
                'icon' => 'document-text',
                'external' => false,
            ]);
        }

        return $results->take(10);
    }

    public function getNavigationItems(): array
    {
        return [
            [
                'type' => 'navigation',
                'label' => __('navigation.dashboard'),
                'description' => __('navigation.dashboard'),
                'url' => route('dashboard'),
                'icon' => 'home',
                'external' => false,
            ],
            [
                'type' => 'navigation',
                'label' => __('navigation.pages'),
                'description' => __('navigation.content_group_heading'),
                'url' => route('admin.pages.index'),
                'icon' => 'document-text',
                'external' => false,
            ],
            [
                'type' => 'navigation',
                'label' => __('navigation.forms'),
                'description' => __('navigation.content_group_heading'),
                'url' => route('admin.forms.index'),
                'icon' => 'document-duplicate',
                'external' => false,
            ],
            [
                'type' => 'navigation',
                'label' => __('navigation.media_library'),
                'description' => __('navigation.content_group_heading'),
                'url' => route('admin.media.index'),
                'icon' => 'photo',
                'external' => false,
            ],
            [
                'type' => 'navigation',
                'label' => __('navigation.settings'),
                'description' => __('navigation.platform'),
                'url' => route('admin.settings.group', 'general'),
                'icon' => 'cog-6-tooth',
                'external' => false,
            ],
            [
                'type' => 'navigation',
                'label' => __('navigation.translations'),
                'description' => __('navigation.platform'),
                'url' => route('admin.translations.index'),
                'icon' => 'language',
                'external' => false,
            ],
            [
                'type' => 'navigation',
                'label' => __('navigation.database_backup'),
                'description' => __('navigation.platform'),
                'url' => route('admin.backup.index'),
                'icon' => 'server-stack',
                'external' => false,
            ],
            [
                'type' => 'navigation',
                'label' => __('navigation.import_export'),
                'description' => __('navigation.platform'),
                'url' => route('admin.import-export.index'),
                'icon' => 'arrow-up-tray',
                'external' => false,
            ],
            [
                'type' => 'navigation',
                'label' => __('navigation.help'),
                'description' => __('navigation.platform'),
                'url' => route('admin.help.index'),
                'icon' => 'question-mark-circle',
                'external' => false,
            ],
            [
                'type' => 'navigation',
                'label' => __('navigation.analytics'),
                'description' => 'Analytics',
                'url' => route('admin.analytics.index'),
                'icon' => 'chart-bar',
                'external' => false,
            ],
            [
                'type' => 'navigation',
                'label' => __('navigation.reports'),
                'description' => 'Analytics',
                'url' => route('admin.reports.index'),
                'icon' => 'document-chart-bar',
                'external' => false,
            ],
        ];
    }

    public function selectItem($index)
    {
        if ($this->searchResults->has($index)) {
            $item = $this->searchResults->get($index);
            
            if ($item['external']) {
                // Open in new tab for external links
                $this->dispatch('open-external-link', url: $item['url']);
            } else {
                // Navigate to internal page
                $this->dispatch('navigate-to', url: $item['url']);
            }
            
            $this->clearSearch();
        }
    }

    public function clearSearch()
    {
        $this->search = '';
        $this->searchResults = collect();
        $this->showResults = false;
    }

    public function render()
    {
        return view('livewire.sidebar-search');
    }
} 