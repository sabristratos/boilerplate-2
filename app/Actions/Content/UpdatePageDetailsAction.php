<?php

namespace App\Actions\Content;

use App\Models\Page;
use Illuminate\Support\Facades\Validator;

class UpdatePageDetailsAction
{
    public function execute(Page $page, array $data): Page
    {
        $validatedData = Validator::make($data, [
            'title' => 'required|array',
            'slug' => 'required|string|max:255',
        ])->validate();

        foreach ($validatedData['title'] as $locale => $value) {
            $page->setTranslation('title', $locale, $value);
        }

        $page->slug = $validatedData['slug'];
        $page->save();

        return $page->refresh();
    }
} 