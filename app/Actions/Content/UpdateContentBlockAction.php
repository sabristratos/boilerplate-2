<?php

namespace App\Actions\Content;

use App\Enums\ContentBlockStatus;
use App\Models\ContentBlock;
use App\Services\BlockManager;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

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

        // Get existing data
        $allTranslations = $contentBlock->getTranslations('data');
        $rawOriginalData = json_decode($contentBlock->getRawOriginal('data'), true) ?? [];
        $nonTranslatableData = Arr::except($rawOriginalData, config('translatable.locales'));

        // Separate incoming data into translatable and non-translatable
        $newTranslatableDataForLocale = [];
        $newNonTranslatableData = [];
        foreach ($data as $key => $value) {
            if (in_array($key, $translatableFields)) {
                $newTranslatableDataForLocale[$key] = $value;
            } else {
                $newNonTranslatableData[$key] = $value;
            }
        }

        // Merge the data
        $allTranslations[$locale] = array_merge(
            $allTranslations[$locale] ?? [],
            $newTranslatableDataForLocale
        );
        $finalNonTranslatableData = array_merge($nonTranslatableData, $newNonTranslatableData);
        $finalData = array_merge($finalNonTranslatableData, $allTranslations);

        // Set the data attribute directly
        $contentBlock->data = $finalData;

        if ($status) {
            $contentBlock->status = $status;
        }

        $contentBlock->save();

        return $contentBlock->refresh();
    }
}