<?php

namespace App\Console\Commands\Translations;

use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;

class CheckCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'translations:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for missing translation keys';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->info('Checking translations...');

        $baseLocale = 'en';
        $otherLocales = collect(File::directories(lang_path()))
            ->map(fn ($dir): string => basename((string) $dir))
            ->filter(fn ($locale): bool => $locale !== $baseLocale);

        if ($otherLocales->isEmpty()) {
            $this->warn('No other locales to compare with.');

            return;
        }

        $baseFiles = collect(File::allFiles(lang_path($baseLocale)))
            ->map(fn ($file): string => $file->getRelativePathname());

        foreach ($baseFiles as $file) {
            try {
                $this->line('');
                $this->line("Checking <fg=yellow>{$file}</>");

                $baseTranslations = Arr::dot(include lang_path($baseLocale.'/'.$file));

                foreach ($otherLocales as $locale) {
                    $localeFilePath = lang_path($locale.'/'.$file);
                    if (! File::exists($localeFilePath)) {
                        $this->warn("File missing for locale <fg=cyan>{$locale}</>: <fg=yellow>{$file}</>");

                        continue;
                    }

                    $localeTranslations = Arr::dot(include $localeFilePath);

                    $missingKeys = array_diff_key($baseTranslations, $localeTranslations);
                    $extraKeys = array_diff_key($localeTranslations, $baseTranslations);

                    if (count($missingKeys) > 0) {
                        $this->warn("Missing keys in <fg=cyan>{$locale}</>:");
                        foreach (array_keys($missingKeys) as $key) {
                            $this->line("- {$key}");
                        }
                    }

                    if (count($extraKeys) > 0) {
                        $this->warn("Extra keys in <fg=cyan>{$locale}</> (not in {$baseLocale}):");
                        foreach (array_keys($extraKeys) as $key) {
                            $this->line("- {$key}");
                        }
                    }

                    if (count($missingKeys) === 0 && count($extraKeys) === 0) {
                        $this->info("No issues found for <fg=cyan>{$locale}</>.");
                    }
                }
            } catch (\Throwable $e) {
                $this->error("Error processing file {$file}: ".$e->getMessage());
            }
        }

        $this->info('Done.');
    }
}
