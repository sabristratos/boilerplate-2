<?php

namespace App\Console\Commands;

use App\Models\Page;
use Illuminate\Console\Command;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

class GenerateSitemap extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sitemap:generate {--format=xml : Output format (xml or txt)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate XML sitemap for the website';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Generating sitemap...');

        $sitemap = Sitemap::create();

        // Add homepage
        $sitemap->add(
            Url::create('/')
                ->setLastModificationDate(now())
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY)
                ->setPriority(1.0)
        );

        // Add published pages
        $pages = Page::where('status', 'published')
            ->where('no_index', false)
            ->get();

        foreach ($pages as $page) {
            $sitemap->add(
                Url::create('/' . $page->slug)
                    ->setLastModificationDate($page->updated_at)
                    ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                    ->setPriority(0.8)
            );
        }

        // Add other important routes
        $this->addImportantRoutes($sitemap);

        // Save sitemap
        $format = $this->option('format');
        $filename = $format === 'txt' ? 'sitemap.txt' : 'sitemap.xml';
        $path = public_path($filename);

        if ($format === 'txt') {
            $this->generateTextSitemap($sitemap, $path);
        } else {
            $sitemap->writeToFile($path);
        }

        $this->info("Sitemap generated successfully at: {$path}");
        $this->info("Total URLs: " . count($sitemap->getTags()));

        return Command::SUCCESS;
    }

    /**
     * Add other important routes to the sitemap.
     */
    private function addImportantRoutes(Sitemap $sitemap): void
    {
        // Add contact page if it exists
        $contactPage = Page::where('slug', 'contact')->where('status', 'published')->first();
        if ($contactPage) {
            $sitemap->add(
                Url::create('/contact')
                    ->setLastModificationDate($contactPage->updated_at)
                    ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY)
                    ->setPriority(0.6)
            );
        }

        // Add about page if it exists
        $aboutPage = Page::where('slug', 'about')->where('status', 'published')->first();
        if ($aboutPage) {
            $sitemap->add(
                Url::create('/about')
                    ->setLastModificationDate($aboutPage->updated_at)
                    ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY)
                    ->setPriority(0.7)
            );
        }

        // Add services page if it exists
        $servicesPage = Page::where('slug', 'services')->where('status', 'published')->first();
        if ($servicesPage) {
            $sitemap->add(
                Url::create('/services')
                    ->setLastModificationDate($servicesPage->updated_at)
                    ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY)
                    ->setPriority(0.7)
            );
        }
    }

    /**
     * Generate a text sitemap (URLs only, one per line).
     */
    private function generateTextSitemap(Sitemap $sitemap, string $path): void
    {
        $urls = [];
        foreach ($sitemap->getTags() as $tag) {
            $urls[] = url($tag->url);
        }

        file_put_contents($path, implode("\n", $urls));
    }
} 