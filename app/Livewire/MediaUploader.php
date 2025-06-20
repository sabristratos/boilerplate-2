<?php

namespace App\Livewire;

use App\Facades\Settings;
use App\Models\Setting;
use Flux\Flux;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Illuminate\Database\Eloquent\Model;
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
    public $file = null;

    /**
     * The URL to fetch media from.
     */
    public $url = null;

    /**
     * Modal visibility states.
     */
    public $showUploadModal = false;
    public $showUrlModal = false;
    public $showExistingMediaModal = false;

    /**
     * Selected media ID.
     */
    public $selectedMediaId = null;

    /**
     * Selected media IDs for checkbox selection.
     */
    public $selectedMediaIds = [];

    /**
     * Search and pagination for existing media.
     */
    public $search = '';
    public $perPage = 12;

    public function mount(Model $model, string $collection = 'default')
    {
        $this->model = $model;
        $this->collection = $collection;
    }

    /**
     * Validation rules.
     */
    protected function rules()
    {
        return [
            'file' => 'nullable|image|max:1024', // 1MB max
            'url' => 'nullable|url',
        ];
    }

    /**
     * Save the media to the setting.
     */
    public function save()
    {
        $this->validate();

        if (!$this->file && !$this->url && !$this->selectedMediaId) {
            Flux::toast('Please upload a file, provide a URL, or select existing media.', variant: 'danger');
            return;
        }

        try {
            $this->model->clearMediaCollection($this->collection);
            $media = null;

            if ($this->file) {
                $media = $this->model->addMedia($this->file->getRealPath())
                    ->usingName($this->file->getClientOriginalName())
                    ->toMediaCollection($this->collection);
            } elseif ($this->url) {
                $media = $this->model->addMediaFromUrl($this->url)
                    ->toMediaCollection($this->collection);
            } elseif ($this->selectedMediaId) {
                $selectedMedia = Media::findOrFail($this->selectedMediaId);
                $media = $selectedMedia->copy($this->model, $this->collection);
            }

            // The component now only needs to know about the model.
            // The parent component is responsible for handling what happens after.
            $this->dispatch('media-updated', modelId: $this->model->id, collection: $this->collection);

            $this->reset(['file', 'url', 'selectedMediaId']);
            $this->showUploadModal = false;
            $this->showUrlModal = false;
            $this->showExistingMediaModal = false;

            Flux::toast('Media saved successfully.', variant: 'success');
        } catch (\Exception $e) {
            Flux::toast('Failed to save media: ' . $e->getMessage(), variant: 'danger');
        }
    }

    /**
     * Select an existing media item.
     */
    public function selectMedia($id)
    {
        try {
            $this->selectedMediaId = $id;
            $this->save();
        } catch (\Exception $e) {
            Flux::toast('Failed to select media: ' . $e->getMessage(), variant: 'danger');
        }
    }

    /**
     * Toggle a media item selection in the checkbox group.
     */
    public function toggleMediaSelection($id)
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
    public function confirmMediaSelection()
    {
        try {
            if (count($this->selectedMediaIds) > 0) {
                $this->selectedMediaId = $this->selectedMediaIds[0];
                $this->save();
            } else {
                Flux::toast('Please select at least one media item.', variant: 'danger');
            }
        } catch (\Exception $e) {
            Flux::toast('Failed to confirm media selection: ' . $e->getMessage(), variant: 'danger');
        }
    }

    /**
     * Reset pagination when search changes.
     */
    public function updatingSearch()
    {
        $this->resetPage();
    }

    /**
     * Remove the media from the setting.
     */
    public function remove()
    {
        try {
            $this->model->clearMediaCollection($this->collection);
            $this->dispatch('media-updated', modelId: $this->model->id, collection: $this->collection);
            Flux::toast('Media removed successfully.', variant: 'success');
        } catch (\Exception $e) {
            Flux::toast('Failed to remove media: ' . $e->getMessage(), variant: 'danger');
        }
    }

    /**
     * Render the component.
     */
    public function render()
    {
        $query = Media::query();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('file_name', 'like', '%' . $this->search . '%')
                  ->orWhere('mime_type', 'like', '%' . $this->search . '%');
            });
        }

        $query->where('mime_type', 'like', 'image/%');

        $media = $query->orderBy('created_at', 'desc')
                      ->paginate($this->perPage);

        return view('livewire.media-uploader', [
            'mediaUrl' => $this->model->getFirstMediaUrl($this->collection),
            'existingMedia' => $media,
        ]);
    }
}
