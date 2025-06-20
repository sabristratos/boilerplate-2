<?php

namespace App\Livewire;

use App\Models\Setting;
use Flux\Flux;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

class MediaUploader extends Component
{
    use WithFileUploads;

    /**
     * The setting model instance.
     */
    public Setting $setting;

    /**
     * The uploaded file.
     */
    public $file = null;

    /**
     * The URL to fetch media from.
     */
    public $url = null;

    /**
     * The active tab (upload or url).
     */
    public $showUploadModal = false;
    public $showUrlModal = false;

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

        if (!$this->file && !$this->url) {
            Flux::toast('Please upload a file or provide a URL.', variant: 'danger');
            return;
        }

        try {
            // Clear existing media
            $this->setting->clearMediaCollection('default');

            if ($this->file) {
                // Add the uploaded file to the media collection
                $this->setting->addMedia($this->file->getRealPath())
                    ->usingName($this->file->getClientOriginalName())
                    ->toMediaCollection('default');
            } elseif ($this->url) {
                // Add the remote file to the media collection
                $this->setting->addMediaFromUrl($this->url)
                    ->toMediaCollection('default');
            }

            // Reset the form
            $this->reset(['file', 'url']);

            $this->showUploadModal = false;
            $this->showUrlModal = false;

            // Notify the parent component that the media has been updated
            $this->dispatch('media-updated', settingKey: $this->setting->key);

            Flux::toast('Media uploaded successfully.', variant: 'success');
        } catch (\Exception $e) {
            Flux::toast('Failed to upload media: ' . $e->getMessage(), variant: 'danger');
        }
    }

    /**
     * Remove the media from the setting.
     */
    public function remove()
    {
        try {
            $this->setting->clearMediaCollection('default');
            $this->dispatch('media-updated', settingKey: $this->setting->key);
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
        return view('livewire.media-uploader', [
            'mediaUrl' => $this->setting->getFirstMediaUrl('default'),
        ]);
    }
}
