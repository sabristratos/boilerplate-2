<?php

namespace App\Actions\Content;

use App\Enums\PublishStatus;
use App\Models\Page;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UpdatePageDetailsAction
{
    public function execute(Page $page, array $data): Page
    {
        $validatedData = Validator::make($data, [
            'title' => ['required', 'array'],
            'slug' => [
                'required',
                'string',
                Rule::unique('pages')->ignore($page->id),
            ],
            'status' => ['required', Rule::enum(PublishStatus::class)],
            'meta_title' => ['nullable', 'array'],
            'meta_description' => ['nullable', 'array'],
            'no_index' => ['boolean'],
        ])->validate();

        foreach ($validatedData['title'] as $locale => $value) {
            $page->setTranslation('title', $locale, $value);
        }

        if (!empty($validatedData['meta_title'])) {
            foreach ($validatedData['meta_title'] as $locale => $value) {
                $page->setTranslation('meta_title', $locale, $value);
            }
        }

        if (!empty($validatedData['meta_description'])) {
            foreach ($validatedData['meta_description'] as $locale => $value) {
                $page->setTranslation('meta_description', $locale, $value);
            }
        }

        $page->slug = $validatedData['slug'];
        $page->status = $validatedData['status'];
        $page->no_index = $validatedData['no_index'];
        $page->save();

        return $page->refresh();
    }
} 