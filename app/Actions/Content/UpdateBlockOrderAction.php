<?php

namespace App\Actions\Content;

use App\Models\ContentBlock;

class UpdateBlockOrderAction
{
    public function execute(array $sortOrder): void
    {
        ContentBlock::setNewOrder($sortOrder);
    }
}
