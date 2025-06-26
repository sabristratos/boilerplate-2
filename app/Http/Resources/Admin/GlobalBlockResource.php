<?php

namespace App\Http\Resources\Admin;

use App\Models\GlobalBlock;
use App\Services\BlockManager;
use App\Services\ResourceSystem\Resource;
use App\Services\ResourceSystem\Fields\Text;
use App\Services\ResourceSystem\Fields\Select;
use App\Services\ResourceSystem\Fields\Textarea; // For data
use App\Services\ResourceSystem\Columns\Column;

class GlobalBlockResource extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = GlobalBlock::class;
    public static $singularLabel = 'Global Block';
    public static $navigationIcon = 'globe-alt';
    public static $navigationGroup = 'Content';
    public static $permission = 'edit content';

    public function fields(): array
    {
        $blockManager = app(BlockManager::class);
        $blockTypes = $blockManager->getAvailableBlocks()
            ->reject(fn($block) => $block->getType() === 'global-block-instance')
            ->mapWithKeys(fn($block) => [$block->getType() => $block->getName()])
            ->toArray();

        $fields = [
            Text::make('name')->rules(['required']),
            Select::make('type')->options($blockTypes)->rules(['required'])->reactive(),
        ];

        $type = null;
        $resourceId = request()->route('id');

        // This handles the initial load of the edit form
        if ($resourceId) {
            $model = static::$model::find($resourceId);
            if ($model) {
                $type = $model->type;
            }
        }

        // This handles reactive updates when the 'type' is changed on the create form
        if (request()->has('components.0.snapshot')) {
            $snapshot = json_decode(request('components.0.snapshot'), true);
            // Ensure we are looking at the correct component's data
            if (data_get($snapshot, 'data.resource') === static::class) {
                 $type = data_get($snapshot, 'data.data.type');
            }
        }

        if ($type) {
            $block = $blockManager->find($type);
            if ($block) {
                // If a type is found, merge its specific fields
                $fields = array_merge($fields, $block->getFields());
            }
        } else if (!$resourceId) {
            // Only on the CREATE form, before a type is selected, show a help text.
            // This avoids rendering the problematic textarea for the 'data' array.
             $fields[1]->helpText('Select a block type to see its configuration fields.');
        }

        return $fields;
    }

    public function columns(): array
    {
        return [
            Column::make('name')->sortable()->searchable(),
            Column::make('type')->sortable(),
            Column::make('updated_at')->sortable()->label('Last Updated'),
        ];
    }
} 