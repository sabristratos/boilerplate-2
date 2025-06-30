<?php

namespace App\Livewire\Translations;

use App\Facades\Settings;
use App\Models\Translation;
use App\Traits\WithToastNotifications;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class ManageTranslations extends Component
{
    use WithFileUploads, WithPagination, WithToastNotifications;

    public $locales = [];

    public $selectedLocales = [];

    public $searchQuery = '';

    public $filterGroup = '';

    public $perPage = 10;

    public $sortBy = 'group';

    public $sortDirection = 'asc';

    public array $translationsData = [];

    public $upload;

    protected $queryString = [
        'searchQuery' => ['except' => ''],
        'filterGroup' => ['except' => ''],
        'perPage' => ['except' => 10],
        'sortBy' => ['except' => 'group'],
        'sortDirection' => ['except' => 'asc'],
        'selectedLocales' => [],
    ];

    public function resetFilters(): void
    {
        $this->reset(['searchQuery', 'filterGroup', 'perPage', 'selectedLocales']);
        $this->selectedLocales = $this->locales;
    }

    public function mount(): void
    {
        // Get available locales from settings
        $availableLocales = Settings::get('general.available_locales', []);
        $this->locales = collect($availableLocales)->pluck('code')->toArray();

        // Populate selectedLocales with all locales by default if not present in query string
        if (empty($this->selectedLocales) && ! request()->has('selectedLocales')) {
            $this->selectedLocales = $this->locales;
        }
    }

    public function updatedSearchQuery(): void
    {
        $this->resetPage();
    }

    public function updatedFilterGroup(): void
    {
        $this->resetPage();
    }

    public function updatedPerPage(): void
    {
        $this->resetPage();
    }

    public function sort($column): void
    {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'asc';
        }
    }

    public function scan(): void
    {
        try {
            Artisan::call('translations:sync-from-files');
            $this->showSuccessToast(__('messages.translations.scan_complete'));
        } catch (Exception $e) {
            $this->showErrorToast($e->getMessage(), __('messages.errors.scan_failed'));
        }
    }

    public function save(): void
    {
        try {
            $this->validate([
                'translationsData.*.*' => 'nullable|string',
            ]);

            foreach ($this->translationsData as $translationId => $locales) {
                $translation = Translation::find($translationId);
                if ($translation) {
                    // Get existing translations
                    $existingTranslations = $translation->getTranslations('text');

                    // Merge updated values
                    $newTranslations = array_merge($existingTranslations, $locales);

                    $translation->setTranslations('text', $newTranslations);
                    $translation->save();
                }
            }

            $this->syncToFiles();
            Artisan::call('cache:clear');

            $this->showSuccessToast(__('messages.translations.save_success'));
        } catch (Exception $e) {
            $this->showErrorToast($e->getMessage(), __('messages.errors.save_failed'));
        }
    }

    public function export()
    {
        try {
            $translations = Translation::all();
            $locales = $this->locales;
            $filename = 'translations-'.now()->format('Y-m-d').'.csv';

            $headers = [
                'Content-type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=$filename",
                'Pragma' => 'no-cache',
                'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
                'Expires' => '0',
            ];

            $callback = function () use ($translations, $locales): void {
                $file = fopen('php://output', 'w');
                $header = ['group', 'key', ...$locales];
                fputcsv($file, $header);

                foreach ($translations as $translation) {
                    $row = [$translation->group, $translation->key];
                    foreach ($locales as $locale) {
                        $row[] = $translation->getTranslation('text', $locale, false);
                    }
                    fputcsv($file, $row);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        } catch (Exception $e) {
            $this->showErrorToast(
                $e->getMessage(),
                __('messages.errors.export_failed')
            );

            return null;
        }
    }

    public function updatedUpload(): void
    {
        try {
            $this->validate([
                'upload' => 'required|file|mimes:csv,txt|max:10240',
            ]);

            $path = $this->upload->getRealPath();
            $file = fopen($path, 'r');
            $header = fgetcsv($file);

            while (($row = fgetcsv($file)) !== false) {
                $data = array_combine($header, $row);
                $group = $data['group'];
                $key = $data['key'];

                $text = [];
                foreach ($this->locales as $locale) {
                    if (isset($data[$locale])) {
                        $text[$locale] = $data[$locale];
                    }
                }

                Translation::updateOrCreate(
                    ['group' => $group, 'key' => $key],
                    ['text' => $text]
                );
            }

            fclose($file);
            $this->upload->delete();
            $this->upload = null;

            $this->syncToFiles();

            $this->showSuccessToast(__('messages.translations.import_success'));
        } catch (Exception $e) {
            $this->showErrorToast(
                $e->getMessage(),
                __('messages.errors.import_failed')
            );
        }
    }

    private function syncToFiles(): void
    {
        $allTranslations = Translation::all();
        $allGroups = $allTranslations->pluck('group')->unique();
        $locales = $this->locales;

        foreach ($locales as $locale) {
            $localePath = lang_path($locale);
            if (! File::exists($localePath)) {
                File::makeDirectory($localePath);
            }

            foreach ($allGroups as $group) {
                $filePath = $localePath.'/'.$group.'.php';

                $groupTranslations = $allTranslations->where('group', $group);

                $output = [];
                foreach ($groupTranslations as $translation) {
                    $translationText = $translation->getTranslation('text', $locale, false);
                    if ($translationText) {
                        Arr::set($output, $translation->key, $translationText);
                    }
                }

                $content = '<?php'.PHP_EOL.PHP_EOL.'return '.var_export($output, true).';'.PHP_EOL;
                File::put($filePath, $content);
            }
        }
    }

    public function render()
    {
        // Get all distinct groups for the filter dropdown
        $groups = Translation::query()
            ->distinct()
            ->pluck('group')
            ->toArray();

        // Build the query with filters
        $query = Translation::query();

        if ($this->searchQuery) {
            $query->where(function ($query): void {
                $query->where('key', 'like', "%{$this->searchQuery}%")
                    ->orWhere('group', 'like', "%{$this->searchQuery}%");

                // Also search in the default locale's text
                if (! empty($this->locales)) {
                    $defaultLocale = Settings::get('general.default_locale', config('app.locale'));
                    $query->orWhereRaw("JSON_EXTRACT(text, '$.\"{$defaultLocale}\"') LIKE ?", ["%{$this->searchQuery}%"]);
                }
            });
        }

        if ($this->filterGroup) {
            $query->where('group', $this->filterGroup);
        }

        // Get paginated results
        $translations = $query->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);

        $this->translationsData = $translations
            ->mapWithKeys(fn ($translation) => [$translation->id => $translation->getTranslations('text')])
            ->toArray();

        return view('livewire.translations.manage-translations', [
            'translations' => $translations,
            'groups' => $groups,
        ]);
    }
}
