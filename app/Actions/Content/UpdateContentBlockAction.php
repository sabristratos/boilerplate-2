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

        $currentData = $contentBlock->getTranslation('data', $locale) ?? [];
        $settings = $contentBlock->settings ?? [];

        foreach ($data as $key => $value) {
            $isTranslatable = in_array($key, $translatableFields);
            if (!$isTranslatable) {
                // Check for wildcard translatable fields (e.g., 'buttons.*.text')
                foreach ($translatableFields as $translatableField) {
                    if (str_ends_with($translatableField, '.*') && str_starts_with($translatableField, $key)) {
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

        if ($status) {
            $contentBlock->status = $status;
        }

        $contentBlock->save();

        return $contentBlock->refresh();
    }
}