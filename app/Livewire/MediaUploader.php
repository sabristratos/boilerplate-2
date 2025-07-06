<?php

namespace App\Livewire;

use App\Models\Setting;
use App\Models\TemporaryMedia;
use Flux\Flux;
use Illuminate\Database\Eloquent\Model;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

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
    public $showUploadModal = false;

    public $showUrlModal = false;

    public $showExistingMediaModal = false;

    /**
     * Selected media ID.
     */
    public $selectedMediaId;

    /**
     * Selected media IDs for checkbox selection.
     */
    public $selectedMediaIds = [];

    /**
     * Search and pagination for existing media.
     */
    public $search = '';

    public $perPage = 12;

    /**
     * Temporary media instance for unsaved models.
     */
    protected $temporaryMedia = null;

    public function mount(Model $model, string $collection = 'default'): void
    {
        $this->model = $model;
        $this->collection = $collection;

        // Initialize temporary media if model is not saved
        if (! $this->model->exists) {
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
            $modelType = get_class($this->model);

            $this->temporaryMedia = TemporaryMedia::getForSession($sessionId, $fieldName);

            if (! $this->temporaryMedia) {
                $this->temporaryMedia = TemporaryMedia::createForSession(
                    $sessionId,
                    $fieldName,
                    $modelType,
                    $this->collection
                );
            }
        } catch (\Exception $e) {
            // Log the error but don't throw it
            \Log::error('Failed to initialize temporary media', [
                'error' => $e->getMessage(),
                'session_id' => session()->getId(),
                'field_name' => $this->collection,
                'model_type' => get_class($this->model),
            ]);
            $this->temporaryMedia = null;
        }
    }

    /**
     * Validation rules.
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

        if (! $this->file && ! $this->url && ! $this->selectedMediaId) {
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
                if (! $this->temporaryMedia) {
                    $this->initializeTemporaryMedia();
                }

                if (! $this->temporaryMedia) {
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
                isTemporary: ! $this->model->exists
            );

            $this->reset(['file', 'url', 'selectedMediaId']);
            $this->showUploadModal = false;
            $this->showUrlModal = false;
            $this->showExistingMediaModal = false;

            Flux::toast('Media saved successfully.', variant: 'success');
        } catch (\Exception $e) {
            Flux::toast('Failed to save media: '.$e->getMessage(), variant: 'danger');
        }
    }

    /**
     * Add media to a model (either actual model or temporary media).
     */
    protected function addMediaToModel($targetModel, ?string $collection = null): ?Media
    {
        $collection = $collection ?? $this->collection;

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
     * Select an existing media item.
     */
    public function selectMedia($id): void
    {
        try {
            $this->selectedMediaId = $id;
            $this->save();
        } catch (\Exception $e) {
            Flux::toast('Failed to select media: '.$e->getMessage(), variant: 'danger');
        }
    }

    /**
     * Toggle a media item selection in the checkbox group.
     */
    public function toggleMediaSelection($id): void
    {
        if (in_array($id, $this->selectedMediaIds)) {
            $this->selectedMediaIds = array_diff($this->selectedMediaIds, [$id]);
        } else {
            $this->selectedMediaIds[] = $id;
        }
    }

    /**
     * Confirm the selected media from checkbox group.
     */
    public function confirmMediaSelection(): void
    {
        try {
            if (count($this->selectedMediaIds) > 0) {
                $this->selectedMediaId = $this->selectedMediaIds[0];
                $this->save();
            } else {
                Flux::toast('Please select at least one media item.', variant: 'danger');
            }
        } catch (\Exception $e) {
            Flux::toast('Failed to confirm media selection: '.$e->getMessage(), variant: 'danger');
        }
    }

    /**
     * Reset pagination when search changes.
     */
    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    /**
     * Remove the media from the setting.
     */
    public function remove(): void
    {
        try {
            if ($this->model->exists) {
                $this->model->clearMediaCollection($this->collection);
            } else {
                // Ensure temporary media is initialized
                if (! $this->temporaryMedia) {
                    $this->initializeTemporaryMedia();
                }

                if (! $this->temporaryMedia) {
                    Flux::toast('Failed to initialize temporary media storage.', variant: 'danger');

                    return;
                }

                $this->temporaryMedia->clearMediaCollection('temp');
            }

            $this->dispatch('media-updated',
                modelId: $this->model->exists ? $this->model->id : null,
                collection: $this->collection,
                isTemporary: ! $this->model->exists
            );
            Flux::toast('Media removed successfully.', variant: 'success');
        } catch (\Exception $e) {
            Flux::toast(__('media.uploader.remove_failed', ['message' => $e->getMessage()]), variant: 'danger');
        }
    }

    /**
     * Render the component.
     */
    public function render()
    {
        // Ensure temporary media is initialized for unsaved models
        if (! $this->model->exists && ! $this->temporaryMedia) {
            $this->initializeTemporaryMedia();
        }

        $query = Media::query();

        if ($this->search) {
            $query->where(function ($q): void {
                $q->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('file_name', 'like', '%'.$this->search.'%')
                    ->orWhere('mime_type', 'like', '%'.$this->search.'%');
            });
        }

        $query->where('mime_type', 'like', 'image/%');

        $media = $query->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        // Only try to get media URL if the model is saved
        $mediaUrl = null;
        if ($this->model->exists) {
            try {
                $mediaUrl = $this->model->getFirstMediaUrl($this->collection);
            } catch (\Exception $e) {
                // Handle the case where the model exists but media URL generation fails
                $mediaUrl = null;
            }
        } elseif ($this->temporaryMedia) {
            try {
                $mediaUrl = $this->temporaryMedia->getFirstMediaUrl('temp');
            } catch (\Exception $e) {
                $mediaUrl = null;
            }
        } else {
            // Try to initialize temporary media if it's null
            try {
                $this->initializeTemporaryMedia();
                if ($this->temporaryMedia) {
                    $mediaUrl = $this->temporaryMedia->getFirstMediaUrl('temp');
                }
            } catch (\Exception $e) {
                $mediaUrl = null;
            }
        }

        return view('livewire.media-uploader', [
            'mediaUrl' => $mediaUrl,
            'existingMedia' => $media,
        ]);
    }
}
