<?php

declare(strict_types=1);

namespace App\Actions\Content;

use App\Models\ContentBlock;
use App\Models\Page;

class UpdateBlockOrderAction
{
    public function execute(Page $page, array $sortOrder): void
    {
        // Get only the blocks that belong to this page
        $pageBlockIds = $page->contentBlocks()->pluck('id')->toArray();

        // Filter the sort order to only include blocks that belong to this page
        $filteredSortOrder = array_filter($sortOrder, function ($id) use ($pageBlockIds) {
            return in_array((int) $id, $pageBlockIds);
        });

        if (! empty($filteredSortOrder)) {
            ContentBlock::setNewOrder($filteredSortOrder);
        }
    }
}
