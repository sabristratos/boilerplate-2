<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Testing pages...\n";

$pages = App\Models\Page::all(['id', 'title']);

echo "Total pages: " . $pages->count() . "\n";

foreach ($pages as $page) {
    echo "Page ID: {$page->id}\n";
    echo "Title: " . json_encode($page->title) . "\n";
    
    // Test the options function
    $locale = app()->getLocale();
    $title = $page->title;
    $displayTitle = is_array($title) ? ($title[$locale] ?? $title['en'] ?? 'Untitled') : $title;
    echo "Display title: {$displayTitle}\n";
    echo "---\n";
} 