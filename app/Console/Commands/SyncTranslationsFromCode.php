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
    protected $signature = 'translations:sync-from-code 
                            {--dry-run : Show what would be changed without making changes}
                            {--backup : Create backup files before modifying}
                            {--paths=* : Specific paths to scan (defaults to app and views)}
                            {--exclude=* : Paths to exclude from scanning}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scan the codebase for translation keys and add missing ones to language files.';

    protected array $foundLiterals = [];

    protected array $foundKeys = [];

    protected array $modifiedFiles = [];

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

        if ($this->option('dry-run')) {
            $this->warn('DRY RUN MODE - No files will be modified');
        }

        $translationKeys = $this->findTranslationKeys();

        if ($translationKeys === []) {
            $this->info('No translation keys found in the codebase.');

            return self::SUCCESS;
        }

        $this->info('Found '.count($translationKeys).' unique translation keys.');

        $this->syncKeysToLanguageFiles($translationKeys);

        if ($this->foundLiterals !== []) {
            $this->warn("\nFound the following string literals used in translation helpers:");
            $this->warn('These should be converted to translation keys with dot notation.');
            $this->table(['Found String Literal'], array_map(fn ($l): array => [$l], array_unique($this->foundLiterals)));
        }

        if ($this->option('dry-run')) {
            $this->info('Dry run completed. No files were modified.');
        } else {
            $this->info('Translation key sync completed successfully.');
            if ($this->modifiedFiles !== []) {
                $this->info('Modified files: '.implode(', ', $this->modifiedFiles));
            }
        }

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

            // Find translation keys with dot notation
            $this->extractTranslationKeys($content, $keys);

            // Find string literals for reporting
            $this->extractStringLiterals($content);

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->info("\nFinished scanning files.");

        return array_unique($keys);
    }

    protected function extractTranslationKeys(string $content, array &$keys): void
    {
        // Match various translation helper patterns
        $patterns = [
            // __('key.subkey')
            '/__\(\s*[\'"]([a-zA-Z0-9_-]+\.[a-zA-Z0-9_.-]+)[\'"]\s*\)/',
            // trans('key.subkey')
            '/trans\(\s*[\'"]([a-zA-Z0-9_-]+\.[a-zA-Z0-9_.-]+)[\'"]\s*\)/',
            // @lang('key.subkey')
            '/@lang\(\s*[\'"]([a-zA-Z0-9_-]+\.[a-zA-Z0-9_.-]+)[\'"]\s*\)/',
            // __('messages.key.subkey')
            '/__\(\s*[\'"](messages\.[a-zA-Z0-9_.-]+)[\'"]\s*\)/',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match_all($pattern, $content, $matches)) {
                foreach ($matches[1] as $match) {
                    if ($this->isValidTranslationKey($match)) {
                        $keys[] = $match;
                    }
                }
            }
        }
    }

    protected function extractStringLiterals(string $content): void
    {
        // Find string literals that should be translation keys
        $patterns = [
            '/__\(\s*[\'"]([a-zA-Z0-9_-]+(?<!\.))[\'"]\s*\)/', // __('literal')
            '/trans\(\s*[\'"]([a-zA-Z0-9_-]+(?<!\.))[\'"]\s*\)/', // trans('literal')
            '/@lang\(\s*[\'"]([a-zA-Z0-9_-]+(?<!\.))[\'"]\s*\)/', // @lang('literal')
        ];

        foreach ($patterns as $pattern) {
            if (preg_match_all($pattern, $content, $matches)) {
                foreach ($matches[1] as $match) {
                    if (! Str::contains($match, '.') && ! in_array($match, $this->foundLiterals)) {
                        $this->foundLiterals[] = $match;
                    }
                }
            }
        }
    }

    protected function isValidTranslationKey(string $key): bool
    {
        // Must contain at least one dot and valid characters
        if (! Str::contains($key, '.') || Str::startsWith($key, '.') || Str::endsWith($key, '.')) {
            return false;
        }

        // Check for valid characters
        return preg_match('/^[a-zA-Z0-9_.-]+$/', $key) === 1;
    }

    protected function getScannableFiles(): array
    {
        $defaultPaths = [
            app_path(),
            resource_path('views'),
            resource_path('js'),
        ];

        $paths = $this->option('paths') ?: $defaultPaths;
        $excludePaths = $this->option('exclude') ?: [
            'vendor',
            'node_modules',
            'tests',
            'storage',
            'bootstrap/cache',
        ];

        $files = collect($paths)->flatMap(function ($path) {
            if (! $this->disk->exists($path)) {
                $this->warn("Path does not exist: {$path}");

                return collect();
            }

            return $this->disk->allFiles($path);
        });

        return $files->filter(function (SplFileInfo $file) use ($excludePaths): bool {
            // Check file extension
            if (! in_array($file->getExtension(), ['php', 'blade.php', 'js', 'vue'])) {
                return false;
            }

            // Check if file should be excluded
            $relativePath = $file->getRelativePathname();
            foreach ($excludePaths as $excludePath) {
                if (Str::contains($relativePath, $excludePath)) {
                    return false;
                }
            }

            return true;
        })->all();
    }

    protected function syncKeysToLanguageFiles(array $keys)
    {
        $this->info('Syncing keys to language files...');
        $locales = $this->disk->directories(lang_path());

        if (empty($locales)) {
            $this->error('No locale directories found in '.lang_path());

            return;
        }

        foreach ($locales as $localePath) {
            $locale = basename((string) $localePath);
            $this->info("Processing locale: {$locale}");

            $translationsByGroup = $this->groupTranslationKeys($keys);

            foreach ($translationsByGroup as $group => $groupKeys) {
                $filePath = lang_path("{$locale}/{$group}.php");
                $this->addMissingKeysToFile($filePath, $group, $groupKeys, $locale);
            }
        }
    }

    protected function groupTranslationKeys(array $keys): array
    {
        $translationsByGroup = [];

        foreach ($keys as $key) {
            if (! $this->isValidTranslationKey($key)) {
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

        return $translationsByGroup;
    }

    protected function addMissingKeysToFile(string $filePath, string $group, array $keys, string $locale)
    {
        $existingTranslations = [];
        if ($this->disk->exists($filePath)) {
            try {
                $existingTranslations = require $filePath;
                if (! is_array($existingTranslations)) {
                    $this->error("Failed to load translations from {$filePath}. File does not return an array. Skipping.");

                    return;
                }
            } catch (\Throwable $e) {
                $this->error("Failed to load translations from {$filePath}: {$e->getMessage()}. Skipping.");

                return;
            }
        }

        $addedKeys = false;
        $missingKeys = [];

        foreach ($keys as $translationKey) {
            if (! Arr::has($existingTranslations, $translationKey)) {
                $missingKeys[] = $translationKey;
                Arr::set($existingTranslations, $translationKey, $this->generatePlaceholderValue($translationKey, $locale));
                $addedKeys = true;
            }
        }

        if ($addedKeys) {
            if ($this->option('dry-run')) {
                $this->line('  <fg=blue>-> Would add '.count($missingKeys)." missing keys to '{$group}' group:</>");
                foreach ($missingKeys as $key) {
                    $this->line("    - {$key}");
                }
            } else {
                if ($this->option('backup') && $this->disk->exists($filePath)) {
                    $this->createBackup($filePath);
                }

                $this->writeTranslationsToFile($filePath, $existingTranslations);
                $this->modifiedFiles[] = basename($filePath);

                $this->line('  <fg=yellow>-> Added '.count($missingKeys)." missing keys to '{$group}' group.</>");
            }
        }
    }

    protected function generatePlaceholderValue(string $key, string $locale): string
    {
        // Generate a meaningful placeholder based on the key
        $parts = explode('.', $key);
        $lastPart = end($parts);

        // Convert camelCase or snake_case to readable text
        $readable = Str::of($lastPart)
            ->replace('_', ' ')
            ->replace('-', ' ')
            ->title()
            ->toString();

        return "[{$readable}]";
    }

    protected function createBackup(string $filePath): void
    {
        $backupPath = $filePath.'.backup.'.date('Y-m-d-H-i-s');
        $this->disk->copy($filePath, $backupPath);
        $this->line('  <fg=green>-> Created backup: '.basename($backupPath).'</>');
    }

    protected function writeTranslationsToFile(string $filePath, array $translations)
    {
        try {
            $content = "<?php\n\nreturn ".$this->arrayToPhpString($translations).";\n";
            $this->disk->put($filePath, $content);
        } catch (\Throwable $e) {
            $this->error("Failed to write translations to {$filePath}: {$e->getMessage()}");
        }
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
