<?php

namespace App\Actions\Content;

use App\Enums\ContentBlockStatus;
use App\Models\ContentBlock;
use App\Services\BlockManager;
use Illuminate\Http\UploadedFile;

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
        if ($imageUpload instanceof \Illuminate\Http\UploadedFile) {
            $contentBlock->addMedia($imageUpload)->toMediaCollection('images');
        }

        $blockClass = $blockManager->find($contentBlock->type);
        $translatableFields = $blockClass instanceof \App\Blocks\Block ? $blockClass->getTranslatableFields() : [];

        $currentData = $contentBlock->getTranslatedData($locale);
        $settings = $contentBlock->getSettingsArray();

        foreach ($data as $key => $value) {
            $isTranslatable = in_array($key, $translatableFields);
            if (! $isTranslatable) {
                // Check for wildcard translatable fields (e.g., 'buttons.*.text')
                foreach ($translatableFields as $translatableField) {
                    if (str_ends_with((string) $translatableField, '.*') && str_starts_with((string) $translatableField, $key)) {
                        $isTranslatable = true;
                        break;
                    }
                }
            }

            if ($isTranslatable) {
                $currentData[$key] = $value;
            } else {
                $settings[$key] = $value;
            }
        }

        $contentBlock->setTranslation('data', $locale, $currentData);
        $contentBlock->settings = $settings;

        if ($status instanceof \App\Enums\ContentBlockStatus) {
            $contentBlock->status = $status;
        }

        $contentBlock->save();

        return $contentBlock->refresh();
    }
}
