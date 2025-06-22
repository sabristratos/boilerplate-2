<?php

namespace Database\Seeders;

use App\Models\Page;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Auth;

class PageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first() ?? User::factory()->create();
        Auth::login($user);

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

        Auth::logout();
    }
}
