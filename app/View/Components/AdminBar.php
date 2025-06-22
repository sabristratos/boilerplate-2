<?php

namespace App\View\Components;

use App\Services\ResourceManager;
use App\Services\SettingsManager;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\Component;

class AdminBar extends Component
{
    public array $resources;

    /**
     * Create a new component instance.
     */
    public function __construct(
        protected SettingsManager $settings,
        protected ResourceManager $resourceManager
    ) {
        $this->resources = $this->resourceManager->getResourcesWithInstances();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        if (Auth::guest() || ! Auth::user()->can('view settings')) {
            return '';
        }

        return view('components.admin-bar', [
            'siteName' => $this->settings->get('site_name', default: config('app.name')),
            'siteLogo' => $this->settings->get('site_logo'),
            'resources' => $this->resources,
        ]);
    }
} 