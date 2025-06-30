<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Symfony\Component\Finder\SplFileInfo;

class SyncTranslationsFromCode extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'translations:sync-from-code';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scan the codebase for translation keys and add missing ones to language files.';

    protected array $foundLiterals = [];

    public function __construct(protected Filesystem $disk)
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Starting translation key sync from code...');

        $translationKeys = $this->findTranslationKeys();
        $this->syncKeysToLanguageFiles($translationKeys);

        if ($this->foundLiterals !== []) {
            $this->warn("\nFound the following string literals used in translation helpers:");
            $this->warn('These should be converted to translation keys with dot notation.');
            $this->table(['Found String Literal'], array_map(fn ($l): array => [$l], array_unique($this->foundLiterals)));
        }

        $this->info('Translation key sync completed successfully.');

        return self::SUCCESS;
    }

    protected function findTranslationKeys(): array
    {
        $this->info('Scanning files for translation keys...');
        $keys = [];
        $files = $this->getScannableFiles();

        $progressBar = $this->output->createProgressBar(count($files));
        $progressBar->start();

        foreach ($files as $file) {
            $content = $file->getContents();
            preg_match_all('/__\(\s*[\'"]([a-zA-Z0-9_-]+\..*?)[\'"]\s*\)/', (string) $content, $matches);

            if (isset($matches[1]) && $matches[1] !== []) {
                $keys = array_merge($keys, $matches[1]);
            }

            // Find literals for reporting
            preg_match_all('/__\(\s*[\'"]([a-zA-Z0-9_-]+(?<!\.)\s*.*?)[\'"]\s*\)/', (string) $content, $literalMatches);
            foreach ($literalMatches[1] as $match) {
                if (! Str::contains($match, '.')) {
                    $this->foundLiterals[] = $match;
                }
            }
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->info("\nFinished scanning files.");

        return array_unique($keys);
    }

    protected function getScannableFiles(): array
    {
        $paths = [
            app_path(),
            resource_path('views'),
        ];

        return collect($paths)->flatMap(fn ($path) => $this->disk->allFiles($path))->filter(fn (SplFileInfo $file): bool => in_array($file->getExtension(), ['php', 'blade.php']))->all();
    }

    protected function syncKeysToLanguageFiles(array $keys)
    {
        $this->info('Syncing keys to language files...');
        $locales = $this->disk->directories(lang_path());

        foreach ($locales as $localePath) {
            $locale = basename((string) $localePath);
            $this->info("Processing locale: {$locale}");

            $translationsByGroup = [];

            foreach ($keys as $key) {
                if (! str_contains((string) $key, '.')) {
                    continue;
                }

                $parts = explode('.', (string) $key);
                $group = $parts[0];
                $translationKey = implode('.', array_slice($parts, 1));
                if ($translationKey === '' || $translationKey === '0') {
                    continue;
                }

                if (! isset($translationsByGroup[$group])) {
                    $translationsByGroup[$group] = [];
                }
                $translationsByGroup[$group][$key] = $translationKey;
            }

            foreach ($translationsByGroup as $group => $groupKeys) {
                $filePath = lang_path("{$locale}/{$group}.php");
                $this->addMissingKeysToFile($filePath, $group, $groupKeys);
            }
        }
    }

    protected function addMissingKeysToFile(string $filePath, string $group, array $keys)
    {
        $existingTranslations = [];
        if ($this->disk->exists($filePath)) {
            $existingTranslations = require $filePath;
            if (! is_array($existingTranslations)) {
                $this->error("Failed to load translations from {$filePath}. Skipping.");

                return;
            }
        }

        $addedKeys = false;
        foreach ($keys as $fullKey => $translationKey) {
            if (! Arr::has($existingTranslations, $translationKey)) {
                Arr::set($existingTranslations, $translationKey, $fullKey);
                $addedKeys = true;
                $this->line("  <fg=yellow>-> Added missing key '{$translationKey}' to '{$group}' group.</>");
            }
        }

        if ($addedKeys) {
            $this->writeTranslationsToFile($filePath, $existingTranslations);
        }
    }

    protected function writeTranslationsToFile(string $filePath, array $translations)
    {
        $content = "<?php\n\nreturn ".$this->arrayToPhpString($translations).";\n";
        $this->disk->put($filePath, $content);
    }

    private function arrayToPhpString(array $array, int $level = 0): string
    {
        $indent = str_repeat('    ', $level + 1);
        $result = "[\n";
        foreach ($array as $key => $value) {
            $result .= "{$indent}'".addslashes($key)."' => ";
            if (is_array($value)) {
                $result .= $this->arrayToPhpString($value, $level + 1).",\n";
            } else {
                $result .= "'".addslashes((string) $value)."',\n";
            }
        }

        return $result.(str_repeat('    ', $level).']');
    }
}
