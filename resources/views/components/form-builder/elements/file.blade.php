@props(['element', 'properties', 'fluxProps'])

@php
    $multiple = $properties['multiple'] ?? false;
    $accept = $properties['accept'] ?? '';
    $maxSize = $properties['maxSize'] ?? '';
    $showPreview = $properties['showPreview'] ?? true;
    
    // Build attributes string
    $attributes = [];
    if ($accept) {
        $attributes[] = 'accept="' . $accept . '"';
    }
    $attributesString = implode(' ', $attributes);
@endphp

<div x-data="{
    files: [],
    handleFileSelect(event) {
        const selectedFiles = Array.from(event.target.files);
        this.files = selectedFiles.map(file => ({
            name: file.name,
            size: file.size,
            type: file.type,
            url: URL.createObjectURL(file)
        }));
    },
    removeFile(index) {
        this.files.splice(index, 1);
        // Update the file input
        const input = event.target.closest('.file-upload-container').querySelector('input[type=file]');
        const dt = new DataTransfer();
        this.files.forEach((file, i) => {
            if (i !== index) {
                // We can't directly add files back, so we'll need to handle this differently
                // For now, we'll just remove from the display
            }
        });
    },
    formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }
}" class="file-upload-container">
    
    <flux:input 
        type="file"
        label="{{ $properties['label'] ?? '' }}" 
        placeholder="{{ $properties['placeholder'] ?? '' }}"
        :multiple="$multiple"
        {!! $attributesString !!}
        @change="handleFileSelect($event)"
    />

    @if($maxSize)
        <flux:text size="sm" variant="subtle" class="mt-1">
            Maximum file size: {{ $maxSize }}
        </flux:text>
    @endif

    @if($showPreview)
        <div x-show="files.length > 0" class="mt-4 space-y-2">
            <flux:heading size="sm">Selected Files:</flux:heading>
            <div class="space-y-2">
                <template x-for="(file, index) in files" :key="index">
                    <div class="flex items-center justify-between p-3 bg-zinc-50 dark:bg-zinc-800 rounded-lg">
                        <div class="flex items-center space-x-3">
                            <div class="flex-shrink-0">
                                <template x-if="file.type.startsWith('image/')">
                                    <img :src="file.url" :alt="file.name" class="w-10 h-10 object-cover rounded">
                                </template>
                                <template x-if="!file.type.startsWith('image/')">
                                    <div class="w-10 h-10 bg-zinc-200 dark:bg-zinc-700 rounded flex items-center justify-center">
                                        <flux:icon name="document" class="w-5 h-5 text-zinc-500" />
                                    </div>
                                </template>
                            </div>
                            <div>
                                <flux:text size="sm" x-text="file.name"></flux:text>
                                <flux:text size="xs" variant="subtle" x-text="formatFileSize(file.size)"></flux:text>
                            </div>
                        </div>
                        <flux:button 
                            size="sm" 
                            variant="ghost" 
                            icon="x-mark"
                            @click="removeFile(index)"
                            tooltip="Remove file"
                        />
                    </div>
                </template>
            </div>
        </div>
    @endif
</div> 