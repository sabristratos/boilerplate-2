<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Testing editBlock data loading...\n\n";

// Find a page with blocks
$page = \App\Models\Page::with('contentBlocks')->first();

if (!$page) {
    echo "No pages found.\n";
    exit;
}

echo "Page: " . $page->getTranslation('title', 'en', false) . "\n";
echo "Blocks count: " . $page->contentBlocks->count() . "\n\n";

foreach ($page->contentBlocks as $block) {
    echo "Block ID: " . $block->id . "\n";
    echo "Block Type: " . $block->type . "\n";
    
    // Get block class
    $blockManager = app(\App\Services\BlockManager::class);
    $blockClass = $blockManager->find($block->type);
    
    // Get default data
    $defaultData = $blockClass instanceof \App\Blocks\Block ? $blockClass->getDefaultData() : [];
    echo "Default data: " . json_encode($defaultData) . "\n";
    
    // Get published data
    $blockData = $block->getTranslatedData('en');
    echo "Published data: " . json_encode($blockData) . "\n";
    
    // Get settings
    $blockSettings = $block->getSettingsArray();
    echo "Published settings: " . json_encode($blockSettings) . "\n";
    
    // Simulate the merge
    $mergedData = array_merge($defaultData, $blockSettings, $blockData);
    echo "Merged data: " . json_encode($mergedData) . "\n";
    
    // Check if any values were overridden
    foreach ($blockData as $key => $value) {
        if (isset($defaultData[$key]) && $defaultData[$key] !== $value) {
            echo "WARNING: Key '$key' has default value '" . $defaultData[$key] . "' but published value is '$value'\n";
        }
    }
    
    echo "---\n\n";
}

echo "Test completed!\n"; 