<?php

namespace App\Services;

use App\Models\Form;
use App\Models\Page;
use App\Services\ResourceSystem\Resource;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use ZipArchive;

class ImportExportService
{
    /**
     * Export a resource to JSON
     */
    public function exportResource(Resource $resource, array $ids = []): array
    {
        $model = $resource::$model;
        $query = $model::query();

        if (! empty($ids)) {
            $query->whereIn('id', $ids);
        }

        $records = $query->get();
        $exportData = [];

        foreach ($records as $record) {
            $data = $record->toArray();

            // Handle media files - get all collections
            if (method_exists($record, 'media') && $record->media !== null) {
                $mediaData = [];
                foreach ($record->media as $media) {
                    $mediaInfo = [
                        'collection_name' => $media->collection_name,
                        'file_name' => $media->file_name,
                        'name' => $media->name,
                        'disk' => $media->disk,
                        'mime_type' => $media->mime_type,
                        'size' => $media->size,
                        'manipulations' => $media->manipulations,
                        'custom_properties' => $media->custom_properties,
                        'responsive_images' => $media->responsive_images,
                        'file_path' => $media->getPath(),
                    ];

                    // Try to get the full path using different methods
                    try {
                        // Method 1: Use Storage facade
                        $mediaInfo['full_path'] = Storage::disk($media->disk)->path($media->getPath());

                        // Method 2: If that doesn't work, try getting the absolute path
                        if (! File::exists($mediaInfo['full_path'])) {
                            $mediaInfo['full_path'] = storage_path('app/public/'.$media->getPath());
                        }

                        // Method 3: Try the original file path
                        if (! File::exists($mediaInfo['full_path'])) {
                            $mediaInfo['full_path'] = $media->getPath();
                        }
                    } catch (\Exception $e) {
                        $mediaInfo['full_path'] = null;
                    }

                    $mediaData[] = $mediaInfo;
                }
                $data['_media'] = $mediaData;
            }

            // Handle relationships
            $relationships = [];
            foreach ($resource->fields() as $field) {
                if (method_exists($field, 'getRelatedModel') && $field->getRelatedModel()) {
                    $relationshipName = Str::camel($field->getName());
                    if (method_exists($record, $relationshipName)) {
                        $related = $record->{$relationshipName};
                        if ($related) {
                            $relationships[$relationshipName] = $related->toArray();
                        }
                    }
                }
            }
            $data['_relationships'] = $relationships;

            $exportData[] = $data;
        }

        return [
            'type' => 'resource',
            'resource_class' => get_class($resource),
            'model_class' => $model,
            'exported_at' => now()->toISOString(),
            'version' => '1.0',
            'data' => $exportData,
        ];
    }

    /**
     * Export pages to JSON
     */
    public function exportPages(array $ids = []): array
    {
        $query = Page::query();

        if (! empty($ids)) {
            $query->whereIn('id', $ids);
        }

        $pages = $query->with('contentBlocks')->get();
        $exportData = [];

        foreach ($pages as $page) {
            $data = $page->toArray();

            // Include content blocks
            $data['content_blocks'] = $page->contentBlocks->map(function ($block) {
                return $block->toArray();
            })->toArray();

            // Handle media files - get all collections
            $mediaData = [];
            if (method_exists($page, 'media') && $page->media !== null) {
                foreach ($page->media as $media) {
                    $mediaInfo = [
                        'collection_name' => $media->collection_name,
                        'file_name' => $media->file_name,
                        'name' => $media->name,
                        'disk' => $media->disk,
                        'mime_type' => $media->mime_type,
                        'size' => $media->size,
                        'manipulations' => $media->manipulations,
                        'custom_properties' => $media->custom_properties,
                        'responsive_images' => $media->responsive_images,
                        'file_path' => $media->getPath(),
                    ];

                    // Try to get the full path using different methods
                    try {
                        // Method 1: Use Storage facade
                        $mediaInfo['full_path'] = Storage::disk($media->disk)->path($media->getPath());

                        // Method 2: If that doesn't work, try getting the absolute path
                        if (! File::exists($mediaInfo['full_path'])) {
                            $mediaInfo['full_path'] = storage_path('app/public/'.$media->getPath());
                        }

                        // Method 3: Try the original file path
                        if (! File::exists($mediaInfo['full_path'])) {
                            $mediaInfo['full_path'] = $media->getPath();
                        }
                    } catch (\Exception $e) {
                        $mediaInfo['full_path'] = null;
                    }

                    $mediaData[] = $mediaInfo;
                }
            }
            $data['_media'] = $mediaData;

            $exportData[] = $data;
        }

        return [
            'type' => 'pages',
            'exported_at' => now()->toISOString(),
            'version' => '1.0',
            'data' => $exportData,
        ];
    }

    /**
     * Export forms to JSON
     */
    public function exportForms(array $ids = []): array
    {
        $query = Form::query();

        if (! empty($ids)) {
            $query->whereIn('id', $ids);
        }

        $forms = $query->with('submissions')->get();
        $exportData = [];

        foreach ($forms as $form) {
            $data = $form->toArray();

            // Include submissions
            $data['submissions'] = $form->submissions->map(function ($submission) {
                return $submission->toArray();
            })->toArray();

            // Handle media files - get all collections (only if model supports media)
            $mediaData = [];
            if (method_exists($form, 'media') && $form->media !== null) {
                foreach ($form->media as $media) {
                    $mediaInfo = [
                        'collection_name' => $media->collection_name,
                        'file_name' => $media->file_name,
                        'name' => $media->name,
                        'disk' => $media->disk,
                        'mime_type' => $media->mime_type,
                        'size' => $media->size,
                        'manipulations' => $media->manipulations,
                        'custom_properties' => $media->custom_properties,
                        'responsive_images' => $media->responsive_images,
                        'file_path' => $media->getPath(),
                    ];

                    // Try to get the full path using different methods
                    try {
                        // Method 1: Use Storage facade
                        $mediaInfo['full_path'] = Storage::disk($media->disk)->path($media->getPath());

                        // Method 2: If that doesn't work, try getting the absolute path
                        if (! File::exists($mediaInfo['full_path'])) {
                            $mediaInfo['full_path'] = storage_path('app/public/'.$media->getPath());
                        }

                        // Method 3: Try the original file path
                        if (! File::exists($mediaInfo['full_path'])) {
                            $mediaInfo['full_path'] = $media->getPath();
                        }
                    } catch (\Exception $e) {
                        $mediaInfo['full_path'] = null;
                    }

                    $mediaData[] = $mediaInfo;
                }
            }
            $data['_media'] = $mediaData;

            $exportData[] = $data;
        }

        return [
            'type' => 'forms',
            'exported_at' => now()->toISOString(),
            'version' => '1.0',
            'data' => $exportData,
        ];
    }

    /**
     * Import resource data
     */
    public function importResource(array $data, bool $overwrite = false): array
    {
        $results = [
            'imported' => 0,
            'skipped' => 0,
            'errors' => [],
        ];

        if (! isset($data['resource_class']) || ! class_exists($data['resource_class'])) {
            $results['errors'][] = 'Invalid resource class';

            return $results;
        }

        $resource = new $data['resource_class'];
        $model = $resource::$model;

        DB::beginTransaction();

        try {
            foreach ($data['data'] as $recordData) {
                $mediaData = $recordData['_media'] ?? [];
                $relationships = $recordData['_relationships'] ?? [];

                // Remove special fields
                unset($recordData['_media'], $recordData['_relationships'], $recordData['id'], $recordData['created_at'], $recordData['updated_at']);

                // Check if record exists
                $existingRecord = null;
                if (isset($recordData['email'])) {
                    $existingRecord = $model::where('email', $recordData['email'])->first();
                } elseif (isset($recordData['slug'])) {
                    $existingRecord = $model::where('slug', $recordData['slug'])->first();
                }

                if ($existingRecord && ! $overwrite) {
                    $results['skipped']++;

                    continue;
                }

                if ($existingRecord && $overwrite) {
                    $record = $existingRecord;
                    $record->fill($recordData);
                } else {
                    $record = new $model($recordData);
                }

                $record->save();

                // Handle relationships
                foreach ($relationships as $relationshipName => $relationshipData) {
                    if (method_exists($record, $relationshipName)) {
                        // This is a simplified relationship import
                        // You might want to enhance this based on your needs
                    }
                }

                // Handle media files
                foreach ($mediaData as $mediaInfo) {
                    $filePath = $mediaInfo['full_path'] ?? $mediaInfo['file_path'] ?? null;
                    if ($filePath && File::exists($filePath)) {
                        $record->addMedia($filePath)
                            ->toMediaCollection($mediaInfo['collection_name'], $mediaInfo['disk'] ?? 'public');
                    }
                }

                $results['imported']++;
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $results['errors'][] = $e->getMessage();
        }

        return $results;
    }

    /**
     * Import pages data
     */
    public function importPages(array $data, bool $overwrite = false): array
    {
        $results = [
            'imported' => 0,
            'skipped' => 0,
            'errors' => [],
        ];

        DB::beginTransaction();

        try {
            foreach ($data['data'] as $pageData) {
                $contentBlocks = $pageData['content_blocks'] ?? [];
                $mediaData = $pageData['_media'] ?? [];

                // Remove special fields
                unset($pageData['content_blocks'], $pageData['_media'], $pageData['id'], $pageData['created_at'], $pageData['updated_at']);

                // Check if page exists
                $existingPage = Page::where('slug', $pageData['slug'])->first();

                if ($existingPage && ! $overwrite) {
                    $results['skipped']++;

                    continue;
                }

                if ($existingPage && $overwrite) {
                    $page = $existingPage;
                    $page->fill($pageData);
                } else {
                    $page = new Page($pageData);
                }

                $page->save();

                // Import content blocks
                foreach ($contentBlocks as $blockData) {
                    unset($blockData['id'], $blockData['page_id'], $blockData['created_at'], $blockData['updated_at']);
                    $page->contentBlocks()->create($blockData);
                }

                // Handle media files
                foreach ($mediaData as $mediaInfo) {
                    $filePath = $mediaInfo['full_path'] ?? $mediaInfo['file_path'] ?? null;
                    if ($filePath && File::exists($filePath)) {
                        $page->addMedia($filePath)
                            ->toMediaCollection($mediaInfo['collection_name'], $mediaInfo['disk'] ?? 'public');
                    }
                }

                $results['imported']++;
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $results['errors'][] = $e->getMessage();
        }

        return $results;
    }

    /**
     * Import forms data
     */
    public function importForms(array $data, bool $overwrite = false): array
    {
        $results = [
            'imported' => 0,
            'skipped' => 0,
            'errors' => [],
        ];

        DB::beginTransaction();

        try {
            foreach ($data['data'] as $formData) {
                $submissions = $formData['submissions'] ?? [];

                // Remove special fields
                unset($formData['submissions'], $formData['id'], $formData['created_at'], $formData['updated_at']);

                // Check if form exists (by name)
                $formName = $formData['name'] ?? null;
                $existingForm = null;

                if ($formName) {
                    $existingForm = Form::where('name->en', $formName)->first();
                }

                if ($existingForm && ! $overwrite) {
                    $results['skipped']++;

                    continue;
                }

                if ($existingForm && $overwrite) {
                    $form = $existingForm;
                    $form->fill($formData);
                } else {
                    $form = new Form($formData);
                    $form->user_id = auth()->id();
                }

                $form->save();

                // Import submissions
                foreach ($submissions as $submissionData) {
                    unset($submissionData['id'], $submissionData['form_id'], $submissionData['created_at'], $submissionData['updated_at']);
                    $form->submissions()->create($submissionData);
                }

                $results['imported']++;
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $results['errors'][] = $e->getMessage();
        }

        return $results;
    }

    /**
     * Create a ZIP file with media files
     */
    public function createExportZip(array $exportData, string $type): string
    {
        $zip = new ZipArchive;
        $filename = "export_{$type}_".now()->format('Y-m-d_H-i-s').'.zip';
        $zipPath = storage_path('app/temp/'.$filename);

        if (! File::exists(dirname($zipPath))) {
            File::makeDirectory(dirname($zipPath), 0755, true);
        }

        if ($zip->open($zipPath, ZipArchive::CREATE) !== true) {
            throw new \Exception('Could not create ZIP file');
        }

        // Add JSON data
        $zip->addFromString('export.json', json_encode($exportData, JSON_PRETTY_PRINT));

        // Add media files
        $mediaFiles = [];
        foreach ($exportData['data'] as $item) {
            if (isset($item['_media'])) {
                foreach ($item['_media'] as $media) {
                    // Try multiple path options
                    $filePath = null;

                    // First try full_path
                    if (isset($media['full_path']) && File::exists($media['full_path'])) {
                        $filePath = $media['full_path'];
                    }
                    // Then try getting the path from storage
                    elseif (isset($media['file_path']) && isset($media['disk'])) {
                        $storagePath = Storage::disk($media['disk'])->path($media['file_path']);
                        if (File::exists($storagePath)) {
                            $filePath = $storagePath;
                        }
                    }
                    // Finally try the file_path directly
                    elseif (isset($media['file_path']) && File::exists($media['file_path'])) {
                        $filePath = $media['file_path'];
                    }

                    if ($filePath) {
                        $mediaFiles[] = [
                            'path' => $filePath,
                            'name' => $media['file_name'] ?? basename($filePath),
                        ];
                    }
                }
            }
        }

        // Add media files to ZIP
        foreach ($mediaFiles as $mediaFile) {
            $zip->addFile($mediaFile['path'], 'media/'.$mediaFile['name']);
        }

        $zip->close();

        return $zipPath;
    }

    /**
     * Extract and validate import file
     */
    public function extractImportFile(string $filePath): array
    {
        $zip = new ZipArchive;

        if ($zip->open($filePath) !== true) {
            throw new \Exception('Could not open ZIP file');
        }

        $tempDir = storage_path('app/temp/import_'.uniqid());
        File::makeDirectory($tempDir, 0755, true);

        $zip->extractTo($tempDir);
        $zip->close();

        $jsonPath = $tempDir.'/export.json';
        if (! File::exists($jsonPath)) {
            throw new \Exception('Export file not found in ZIP');
        }

        $data = json_decode(File::get($jsonPath), true);
        if (! $data) {
            throw new \Exception('Invalid JSON data');
        }

        return [
            'data' => $data,
            'temp_dir' => $tempDir,
        ];
    }

    /**
     * Clean up temporary files
     */
    public function cleanupTempFiles(string $tempDir): void
    {
        if (File::exists($tempDir)) {
            File::deleteDirectory($tempDir);
        }
    }
}
