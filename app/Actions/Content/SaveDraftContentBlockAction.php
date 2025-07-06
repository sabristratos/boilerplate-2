<?php

namespace App\Actions\Content;

use App\Models\ContentBlock;
use App\Services\BlockManager;
use Illuminate\Http\UploadedFile;

class SaveDraftContentBlockAction
{
    public function execute(
        ContentBlock $contentBlock,
        array $data,
        string $locale,
        ?bool $visible,
        ?UploadedFile $imageUpload,
        BlockManager $blockManager
    ): ContentBlock {
        if ($imageUpload instanceof \Illuminate\Http\UploadedFile) {
            $contentBlock->addMedia($imageUpload)->toMediaCollection('images');
        }

        $blockClass = $blockManager->find($contentBlock->type);
        $translatableFields = $blockClass instanceof \App\Blocks\Block ? $blockClass->getTranslatableFields() : [];

        $currentDraftData = $contentBlock->getDraftTranslatedData($locale);
        $currentDraftSettings = $contentBlock->getDraftSettingsArray();

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
                $currentDraftData[$key] = $value;
            } else {
                $currentDraftSettings[$key] = $value;
            }
        }

        $contentBlock->setTranslation('draft_data', $locale, $currentDraftData);
        $contentBlock->draft_settings = $currentDraftSettings;
        $contentBlock->last_draft_at = now();

        // Always set draft visibility if provided
        if ($visible !== null) {
            $contentBlock->draft_visible = $visible;
        }

        $contentBlock->save();

        return $contentBlock->refresh();
    }
}
