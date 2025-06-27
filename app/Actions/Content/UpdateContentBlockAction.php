<?php

namespace App\Actions\Content;

use App\Enums\ContentBlockStatus;
use App\Models\ContentBlock;
use App\Services\BlockManager;
use Illuminate\Http\UploadedFile;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UpdateContentBlockAction
{
    public function execute(
        ContentBlock $contentBlock,
        array $data,
        string $locale,
        ?ContentBlockStatus $status,
        ?UploadedFile $imageUpload,
        BlockManager $blockManager
    ): ContentBlock {
        if ($imageUpload) {
            $contentBlock->addMedia($imageUpload)->toMediaCollection('images');
        }

        $blockClass = $blockManager->find($contentBlock->type);
        $translatableFields = $blockClass ? $blockClass->getTranslatableFields() : [];

        // Separate incoming data into translatable and non-translatable
        $translatableDataForLocale = [];
        $nonTranslatableData = [];
        foreach ($data as $key => $value) {
            if (in_array($key, $translatableFields)) {
                $translatableDataForLocale[$key] = $value;
            } else {
                $nonTranslatableData[$key] = $value;
            }
        }

        // Save non-translatable data to the settings column
        $contentBlock->settings = array_merge($contentBlock->settings ?? [], $nonTranslatableData);

        // Get all existing translations for the 'data' attribute
        $allDataTranslations = $contentBlock->getTranslations('data');

        // Get existing data for the current locale, or an empty array if none exists
        $existingDataForLocale = $allDataTranslations[$locale] ?? [];

        // Merge the updated translatable data with the existing data for the current locale
        $mergedData = array_merge($existingDataForLocale, $translatableDataForLocale);

        // Set the merged data back for the current locale
        $allDataTranslations[$locale] = $mergedData;

        // Save all translations for the 'data' attribute
        $contentBlock->setTranslations('data', $allDataTranslations);

        if ($status) {
            $contentBlock->status = $status;
        }

        $contentBlock->save();

        return $contentBlock->refresh();
    }
}