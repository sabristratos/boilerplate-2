<?php

namespace Database\Seeders;

use App\Facades\Settings;
use App\Models\Page;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Homepage
        $home = Page::create([
            'title' => [
                'en' => 'Homepage',
                'fr' => 'Page d\'accueil',
            ],
            'slug' => 'homepage',
        ]);

        $home->contentBlocks()->create([
            'type' => 'hero-section',
            'data' => [
                'heading' => [
                    'en' => 'Welcome to the boilerplate',
                    'fr' => 'Bienvenue sur le boilerplate',
                ],
                'subheading' => [
                    'en' => 'This is a starting point for your new project.',
                    'fr' => 'Ceci est un point de départ pour votre nouveau projet.',
                ],
            ],
        ]);

        Settings::set('general.homepage', $home->id);

        // About Us page
        $aboutTitle = [
            'en' => 'About Us',
            'fr' => 'À propos de nous',
        ];
        $about = Page::create([
            'title' => $aboutTitle,
            'slug' => Str::slug($aboutTitle['en']),
        ]);

        $about->contentBlocks()->create([
            'type' => 'content-area',
            'data' => [
                'content' => [
                    'en' => 'This is the about us page.',
                    'fr' => 'Ceci est la page à propos de nous.',
                ],
            ],
        ]);
    }
}
