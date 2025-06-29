<?php

namespace App\Actions\Content;

use App\Models\ContentBlock;

class DeleteContentBlockAction
{
    public function execute(int $blockId): void
    {
        ContentBlock::find($blockId)?->delete();
    }
} 