<?php

declare(strict_types=1);

namespace App\Livewire;

use App\DTOs\MediaDTO;
use App\DTOs\DTOFactory;
use App\Models\Setting;
use App\Models\TemporaryMedia;
use App\Services\Contracts\MediaServiceInterface;
use Flux\Flux;
use Illuminate\Database\Eloquent\Model;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * Livewire component for media upload and management.
 *
 * This component provides media upload functionality with support for
 * file uploads, URL imports, and existing media selection. It uses
 * DTOs and services for data handling and business logic.
 */
class MediaUploader extends Component
{
    use WithFileUploads;
    use WithPagination;

    /**
     * The model instance.
     */
    public Model $model;

    /**
     * The media collection name.
     */
    public string $collection;

    /**
     * The uploaded file.
     */
    public $file;

    /**
     * The URL to fetch media from.
     */
    public $url;

    /**
     * Modal visibility states.
     */
    public bool $showUploadModal = false;

    public bool $showUrlModal = false;

    public bool $showExistingMediaModal = false;

    /**
     * Selected media ID.
     */
    public ?int $selectedMediaId = null;

    /**
     * Selected media IDs for checkbox selection.
     *
     * @var array<int>
     */
    public array $selectedMediaIds = [];

    /**
     * Search and pagination for existing media.
     */
    public string $search = '';

    public int $perPage = 12;

    /**
     * Temporary media instance for unsaved models.
     */
    protected ?TemporaryMedia $temporaryMedia = null;

    /**
     * Media service instance.
     */
    protected MediaServiceInterface $mediaService;

    /**
     * Boot the component with dependencies.
     */
    public function boot(MediaServiceInterface $mediaService): void
    {
        $this->mediaService = $mediaService;
    }

    /**
     * Mount the component with the model and collection.
     */
    public function mount(Model $model, string $collection = 'default'): void
    {
        $this->model = $model;
        $this->collection = $collection;

        // Initialize temporary media if model is not saved
        if (!$this->model->exists) {
            $this->initializeTemporaryMedia();
        }
    }

    /**
     * Initialize temporary media for unsaved models.
     */
    protected function initializeTemporaryMedia(): void
    {
        try {
            $sessionId = session()->getId();
            $fieldName = $this->collection;
            $modelType = $this->model::class;

            // Clear any existing temporary media for this session and field
            // This ensures a fresh start for new resource creation
            TemporaryMedia::clearForSession($sessionId, $fieldName);

            $this->temporaryMedia = TemporaryMedia::createForSession(
                $sessionId,
                $fieldName,
                $modelType,
                $this->collection
            );
        } catch (\Exception $e) {
            logger()->error('Failed to initialize temporary media', [
                'error' => $e->getMessage(),
                'session_id' => session()->getId(),
                'field_name' => $this->collection,
                'model_type' => $this->model::class,
            ]);
            $this->temporaryMedia = null;
        }
    }

    /**
     * Validation rules.
     *
     * @return array<string, array<string>>
     */
    protected function rules(): array
    {
        return [
            'file' => 'nullable|image|max:1024', // 1MB max
            'url' => 'nullable|url',
        ];
    }

    /**
     * Save the media to the setting.
     */
    public function save(): void
    {
        $this->validate();

        if (!$this->file && !$this->url && !$this->selectedMediaId) {
            Flux::toast('Please upload a file, provide a URL, or select existing media.', variant: 'danger');
            return;
        }

        try {
            if ($this->model->exists) {
                // Model is saved, add media directly
                $this->model->clearMediaCollection($this->collection);
                $media = $this->addMediaToModel($this->model);
            } else {
                // Model is not saved, use temporary media
                if (!$this->temporaryMedia) {
                    $this->initializeTemporaryMedia();
                }

                if (!$this->temporaryMedia) {
                    Flux::toast('Failed to initialize temporary media storage.', variant: 'danger');
                    return;
                }

                $this->temporaryMedia->clearMediaCollection('temp');
                $media = $this->addMediaToModel($this->temporaryMedia, 'temp');
            }

            // The component now only needs to know about the model.
            // The parent component is responsible for handling what happens after.
            $this->dispatch('media-updated',
                modelId: $this->model->exists ? $this->model->id : null,
                collection: $this->collection,
                isTemporary: !$this->model->exists
            );

            $this->reset(['file', 'url', 'selectedMediaId']);
            $this->showUploadModal = false;
            $this->showUrlModal = false;
            $this->showExistingMediaModal = false;

            Flux::toast('Media saved successfully.', variant: 'success');
        } catch (\Exception $e) {
            logger()->error('Failed to save media', [
                'error' => $e->getMessage(),
                'model_id' => $this->model->exists ? $this->model->id : null,
                'collection' => $this->collection,
            ]);
            
            Flux::toast('Failed to save media: '.$e->getMessage(), variant: 'danger');
        }
    }

    /**
     * Add media to a model (either actual model or temporary media).
     */
    protected function addMediaToModel($targetModel, ?string $collection = null): ?Media
    {
        $collection ??= $this->collection;

        if ($this->file) {
            return $targetModel->addMedia($this->file->getRealPath())
                ->usingName($this->file->getClientOriginalName())
                ->toMediaCollection($collection);
        } elseif ($this->url) {
            return $targetModel->addMediaFromUrl($this->url)
                ->toMediaCollection($collection);
        } elseif ($this->selectedMediaId) {
            $selectedMedia = Media::findOrFail($this->selectedMediaId);
            return $selectedMedia->copy($targetModel, $collection);
        }

        return null;
    }

    /**
     * Select media for use.
     */
    public function selectMedia(int $id): void
    {
        $this->selectedMediaId = $id;
        $this->showExistingMediaModal = false;
    }

    /**
     * Toggle media selection for multiple selection.
     */
    public function toggleMediaSelection(int $id): void
    {
        if (in_array($id, $this->selectedMediaIds)) {
            $this->selectedMediaIds = array_values(array_filter($this->selectedMediaIds, fn($mediaId) => $mediaId !== $id));
        } else {
            $this->selectedMediaIds[] = $id;
        }
    }

    /**
     * Confirm selection of multiple media items.
     */
    public function confirmMediaSelection(): void
    {
        if (empty($this->selectedMediaIds)) {
            Flux::toast('Please select at least one media item.', variant: 'warning');
            return;
        }

        $this->selectedMediaId = $this->selectedMediaIds[0]; // Use first selected
        $this->selectedMediaIds = [];
        $this->showExistingMediaModal = false;
    }

    /**
     * Handle search updates.
     */
    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    /**
     * Remove media from the collection.
     */
    public function remove(): void
    {
        try {
            if ($this->model->exists) {
                $this->model->clearMediaCollection($this->collection);
            } else {
                $this->temporaryMedia?->clearMediaCollection('temp');
            }

            $this->dispatch('media-updated',
                modelId: $this->model->exists ? $this->model->id : null,
                collection: $this->collection,
                isTemporary: !$this->model->exists
            );

            Flux::toast('Media removed successfully.', variant: 'success');
        } catch (\Exception $e) {
            logger()->error('Failed to remove media', [
                'error' => $e->getMessage(),
                'model_id' => $this->model->exists ? $this->model->id : null,
                'collection' => $this->collection,
            ]);
            
            Flux::toast('Failed to remove media: '.$e->getMessage(), variant: 'danger');
        }
    }

    /**
     * Get media DTO for the current collection.
     */
    public function getCurrentMediaDTO(): ?MediaDTO
    {
        if ($this->model->exists) {
            $media = $this->model->getFirstMedia($this->collection);
        } else {
            $media = $this->temporaryMedia?->getFirstMedia('temp');
        }

        return $media ? DTOFactory::createMediaDTO($media) : null;
    }

    /**
     * Get available media for selection.
     */
    public function getAvailableMedia()
    {
        $query = Media::query();

        if ($this->search) {
            $query->where('name', 'like', '%'.$this->search.'%');
        }

        return $query->orderBy('created_at', 'desc')->paginate($this->perPage);
    }

    /**
     * Render the component.
     */
    public function render()
    {
        $currentMedia = $this->getCurrentMediaDTO();
        $availableMedia = $this->getAvailableMedia();

        return view('livewire.media-uploader', [
            'currentMedia' => $currentMedia,
            'availableMedia' => $availableMedia,
        ]);
    }
}
