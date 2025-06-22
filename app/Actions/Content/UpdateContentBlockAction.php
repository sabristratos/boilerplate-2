<?php

namespace App\Actions\Content;

use App\Models\ContentBlock;
use Illuminate\Http\UploadedFile;

class UpdateContentBlockAction
{
    public function execute(ContentBlock $contentBlock, array $data, ?UploadedFile $imageUpload): ContentBlock
    {
        if ($imageUpload) {
            $contentBlock->addMedia($imageUpload)->toMediaCollection('images');
        }

        $contentBlock->update(['data' => $data]);

        return $contentBlock->refresh();
    }
} 