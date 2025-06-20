<?php

namespace App\Console\Commands\Translations;

use App\Models\Translation;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Arr;

class SyncFromDb extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'translations:sync-from-db';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronize translations from the database to language files.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Synchronizing translations from database to files...');

        $translations = Translation::all();
        $locales = $this->getLocales($translations);

        if ($locales->isEmpty()) {
            $this->warn('No locales found in translations data.');
            return;
        }

        foreach ($locales as $locale) {
            $this->line('Processing locale: ' . $locale);
            $localePath = lang_path($locale);

            if (!File::exists($localePath)) {
                File::makeDirectory($localePath);
            }

            $groupedTranslations = $translations->groupBy('group');

            foreach ($groupedTranslations as $group => $groupTranslations) {
                $filePath = $localePath . '/' . $group . '.php';
                $output = [];

                foreach ($groupTranslations as $translation) {
                    $translationText = $translation->getTranslation('text', $locale, false);
                    if ($translationText) {
                        Arr::set($output, $translation->key, $translationText);
                    }
                }

                if (!empty($output)) {
                    $content = '<?php' . PHP_EOL . PHP_EOL . 'return ' . var_export($output, true) . ';' . PHP_EOL;
                    File::put($filePath, $content);
                }
            }
        }

        $this->info('Synchronization complete.');
    }

    protected function getLocales($translations)
    {
        $locales = collect();
        $translations->each(function ($translation) use ($locales) {
            if (is_array($translation->text)) {
                foreach (array_keys($translation->text) as $locale) {
                    if (!$locales->contains($locale)) {
                        $locales->push($locale);
                    }
                }
            }
        });
        return $locales;
    }
}
