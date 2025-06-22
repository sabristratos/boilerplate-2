<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $page = Page::create([
            'title' => [
                'en' => 'About Us',
                'fr' => 'À propos de nous',
            ],
            'slug' => [
                'en' => 'about-us',
                'fr' => 'a-propos-de-nous',
            ],
        ]);

        $page->contentBlocks()->create([
            'type' => 'hero-section',
            'data' => [
                'heading' => 'Welcome to Our Company',
                'subheading' => 'Learn more about what makes us special.',
            ],
        ]);
    }
}
