<?php

namespace App\Livewire\Admin;

use App\Services\ImportExportService;
use App\Services\ResourceManager;
use App\Traits\WithToastNotifications;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('components.layouts.app')]
class ImportExport extends Component
{
    use WithToastNotifications, WithFileUploads;

    public string $activeTab = 'export';
    public string $selectedType = 'resources';
    public string $selectedResource = '';
    public array $selectedIds = [];
    public bool $includeMedia = true;
    
    // Import properties
    public $importFile;
    public bool $overwriteExisting = false;
    public array $importResults = [];
    public bool $showImportResults = false;

    protected ImportExportService $importExportService;
    protected ResourceManager $resourceManager;

    public function boot(ImportExportService $importExportService, ResourceManager $resourceManager)
    {
        $this->importExportService = $importExportService;
        $this->resourceManager = $resourceManager;
    }

    public function mount(): void
    {
        // Handle URL parameters for pre-selection
        $request = request();
        
        if ($request->has('tab')) {
            $this->activeTab = $request->get('tab');
        }
        
        if ($request->has('type')) {
            $this->selectedType = $request->get('type');
        }
        
        if ($request->has('resource')) {
            $this->selectedResource = $request->get('resource');
        }
    }

    public function getResourcesProperty()
    {
        return $this->resourceManager->getResourcesWithInstances();
    }

    public function getResourceDataProperty()
    {
        if ($this->selectedType === 'resources' && $this->selectedResource) {
            $resource = new $this->selectedResource;
            $model = $resource::$model;
            return $model::all()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->name ?? $item->email ?? $item->title ?? "ID: {$item->id}",
                ];
            })->toArray();
        }

        if ($this->selectedType === 'pages') {
            return \App\Models\Page::all()->map(function ($page) {
                return [
                    'id' => $page->id,
                    'name' => $page->getTranslation('title', app()->getLocale()) ?? $page->slug,
                ];
            })->toArray();
        }

        if ($this->selectedType === 'forms') {
            return \App\Models\Form::all()->map(function ($form) {
                return [
                    'id' => $form->id,
                    'name' => $form->getTranslation('name', app()->getLocale()) ?? "Form ID: {$form->id}",
                ];
            })->toArray();
        }

        return [];
    }

    public function export()
    {
        try {
            $exportData = [];

            if ($this->selectedType === 'resources' && $this->selectedResource) {
                $resource = new $this->selectedResource;
                $exportData = $this->importExportService->exportResource($resource, $this->selectedIds);
            } elseif ($this->selectedType === 'pages') {
                $exportData = $this->importExportService->exportPages($this->selectedIds);
            } elseif ($this->selectedType === 'forms') {
                $exportData = $this->importExportService->exportForms($this->selectedIds);
            }

            if (empty($exportData['data'])) {
                $this->showErrorToast('No data to export', 'Export failed');
                return;
            }
            
            // Create ZIP file if media is included
            if ($this->includeMedia) {
                $zipPath = $this->importExportService->createExportZip($exportData, $this->selectedType);
                
                return response()->download($zipPath)->deleteFileAfterSend();
            } else {
                // Return JSON file
                $filename = "export_{$this->selectedType}_" . now()->format('Y-m-d_H-i-s') . '.json';
                
                return response()->streamDownload(function () use ($exportData) {
                    echo json_encode($exportData, JSON_PRETTY_PRINT);
                }, $filename, [
                    'Content-Type' => 'application/json',
                ]);
            }

        } catch (\Exception $e) {
            $this->showErrorToast($e->getMessage(), 'Export failed');
        }
    }

    public function import()
    {
        $this->validate([
            'importFile' => 'required|file|mimes:zip,json|max:10240', // 10MB max
        ]);

        try {
            $tempDir = null;
            $data = null;

            if ($this->importFile->getClientOriginalExtension() === 'zip') {
                $tempPath = $this->importFile->store('temp');
                $extracted = $this->importExportService->extractImportFile(storage_path('app/' . $tempPath));
                $data = $extracted['data'];
                $tempDir = $extracted['temp_dir'];
            } else {
                $data = json_decode($this->importFile->get(), true);
            }

            if (!$data) {
                throw new \Exception('Invalid import file format');
            }

            $results = [];

            if ($data['type'] === 'resource') {
                $results = $this->importExportService->importResource($data, $this->overwriteExisting);
            } elseif ($data['type'] === 'pages') {
                $results = $this->importExportService->importPages($data, $this->overwriteExisting);
            } elseif ($data['type'] === 'forms') {
                $results = $this->importExportService->importForms($data, $this->overwriteExisting);
            } else {
                throw new \Exception('Unknown import type');
            }

            $this->importResults = $results;
            $this->showImportResults = true;

            // Clean up
            if ($tempDir) {
                $this->importExportService->cleanupTempFiles($tempDir);
            }
            if (isset($tempPath)) {
                \Illuminate\Support\Facades\Storage::delete($tempPath);
            }

            $this->reset('importFile');

            $message = "Import completed: {$results['imported']} imported, {$results['skipped']} skipped";
            if (!empty($results['errors'])) {
                $message .= ", " . count($results['errors']) . " errors";
            }

            $this->showSuccessToast($message, 'Import completed');

        } catch (\Exception $e) {
            $this->showErrorToast($e->getMessage(), 'Import failed');
        }
    }

    public function updatedSelectedType()
    {
        $this->selectedResource = '';
        $this->selectedIds = [];
    }

    public function updatedSelectedResource()
    {
        $this->selectedIds = [];
    }

    public function selectAll()
    {
        $this->selectedIds = collect($this->resourceData)->pluck('id')->toArray();
    }

    public function deselectAll()
    {
        $this->selectedIds = [];
    }

    public function render()
    {
        return view('livewire.admin.import-export')
            ->title(__('messages.import_export.title'));
    }
} 