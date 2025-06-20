<?php

namespace App\Livewire\Translations;

use App\Facades\Settings;
use App\Models\Translation;
use Exception;
use Flux\Flux;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class ManageTranslations extends Component
{
    use WithPagination, WithFileUploads;

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

    public function resetFilters()
    {
        $this->reset(['searchQuery', 'filterGroup', 'perPage', 'selectedLocales']);
        $this->selectedLocales = $this->locales;
    }

    public function mount()
    {
        // Get available locales from settings
        $availableLocales = Settings::get('general.available_locales', []);
        $this->locales = collect($availableLocales)->pluck('code')->toArray();

        // Populate selectedLocales with all locales by default if not present in query string
        if (empty($this->selectedLocales) && ! request()->has('selectedLocales')) {
            $this->selectedLocales = $this->locales;
        }
    }

    public function updatedSearchQuery()
    {
        $this->resetPage();
    }

    public function updatedFilterGroup()
    {
        $this->resetPage();
    }

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    public function sort($column)
    {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'asc';
        }
    }

    public function scan()
    {
        try {
            Artisan::call('translations:sync-from-files');
            Flux::toast(
                heading: 'Scan Complete',
                text: 'Files scanned and translations updated.',
                variant: 'success'
            );
        } catch (Exception $e) {
            Flux::toast(
                heading: 'Scan Failed',
                text: $e->getMessage(),
                variant: 'danger'
            );
        }

        return $this->redirect(request()->header('Referer'));
    }

    public function save()
    {
        try {
            foreach ($this->translationsData as $translationId => $locales) {
                $translation = Translation::find($translationId);
                if ($translation) {
                    $translation->setTranslations('text', $locales);
                    $translation->save();
                }
            }

            $this->syncToFiles();
            Artisan::call('cache:clear');

            Flux::toast(
                heading: 'Save successful',
                text: 'Translations saved, cache cleared, and files synced.',
                variant: 'success'
            );
        } catch (Exception $e) {
            Flux::toast(
                heading: 'Save failed',
                text: $e->getMessage(),
                variant: 'danger'
            );
        }
    }

    public function export()
    {
        try {
            $translations = Translation::all();
            $locales = $this->locales;
            $filename = 'translations-' . now()->format('Y-m-d') . '.csv';

            $headers = [
                'Content-type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=$filename",
                'Pragma' => 'no-cache',
                'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
                'Expires' => '0',
            ];

            $callback = function () use ($translations, $locales) {
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
            Flux::toast(
                heading: 'Export failed',
                text: $e->getMessage(),
                variant: 'danger'
            );

            return null;
        }
    }

    public function updatedUpload()
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

            Flux::toast(
                heading: 'Import successful',
                text: 'Translations imported and synced to files.',
                variant: 'success'
            );
        } catch (Exception $e) {
            Flux::toast(
                heading: 'Import failed',
                text: $e->getMessage(),
                variant: 'danger'
            );
        }
    }

    private function syncToFiles()
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
                $filePath = $localePath . '/' . $group . '.php';

                $groupTranslations = $allTranslations->where('group', $group);

                $output = [];
                foreach ($groupTranslations as $translation) {
                    $translationText = $translation->getTranslation('text', $locale, false);
                    if ($translationText) {
                        Arr::set($output, $translation->key, $translationText);
                    }
                }

                $content = '<?php' . PHP_EOL . PHP_EOL . 'return ' . var_export($output, true) . ';' . PHP_EOL;
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
            $query->where(function ($query) {
                $query->where('key', 'like', "%{$this->searchQuery}%")
                      ->orWhere('group', 'like', "%{$this->searchQuery}%");

                // Also search in the default locale's text
                if (!empty($this->locales)) {
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
            ->mapWithKeys(function ($translation) {
                return [$translation->id => $translation->getTranslations('text')];
            })
            ->toArray();

        return view('livewire.translations.manage-translations', [
            'translations' => $translations,
            'groups' => $groups,
        ]);
    }
}
