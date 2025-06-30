<?php

namespace App\Console\Commands\Translations;

use App\Facades\Settings;
use App\Models\Translation;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;

class SyncFromFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'translations:sync-from-files';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronize translations from language files to the database.';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->info('Truncating translations table...');
        Translation::truncate();

        $this->info('Synchronizing translations from files to database...');

        $availableLocales = Settings::get('general.available_locales', []);
        $locales = collect($availableLocales)->pluck('code');

        if ($locales->isEmpty()) {
            $this->warn('No language directories found.');

            return;
        }

        $defaultLocale = Settings::get('general.default_locale', config('app.locale'));
        if ($locales->doesntContain($defaultLocale)) {
            $this->error("Default locale '{$defaultLocale}' not found in language directories.");

            return;
        }

        $defaultLangPath = lang_path($defaultLocale);
        $files = File::files($defaultLangPath);

        foreach ($files as $file) {
            $group = File::name($file);
            $this->line(__('commands.sync_from_files.processing_group', ['group' => $group]));

            $translations = Arr::dot(include $file);

            foreach ($translations as $key => $value) {
                $translationsData = [];
                foreach ($locales as $locale) {
                    $localeFilePath = lang_path($locale.'/'.$group.'.php');
                    if (File::exists($localeFilePath)) {
                        $localeTranslations = Arr::dot(include $localeFilePath);
                        if (isset($localeTranslations[$key])) {
                            $translationsData[$locale] = $localeTranslations[$key];
                        }
                    }
                }

                if (! empty($translationsData)) {
                    Translation::updateOrCreate(
                        ['group' => $group, 'key' => $key],
                        ['text' => $translationsData]
                    );
                }
            }
        }

        $this->info('Synchronization complete.');
    }
}
