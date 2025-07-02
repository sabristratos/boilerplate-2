<?php

namespace App\Actions\Content;

use App\Enums\PublishStatus;
use App\Models\Page;

class SaveDraftPageDetailsAction
{
    public function execute(Page $page, array $data): Page
    {
        // Save draft title translations
        if (isset($data['title'])) {
            foreach ($data['title'] as $locale => $title) {
                if (!empty($title)) {
                    $page->setTranslation('draft_title', $locale, $title);
                }
            }
        }

        // Save draft slug
        if (isset($data['slug']) && !empty($data['slug'])) {
            $page->draft_slug = $data['slug'];
        }

        // Save draft status
        if (isset($data['status']) && $data['status'] instanceof PublishStatus) {
            $page->status = $data['status'];
        }

        // Save draft meta translations
        if (isset($data['meta_title'])) {
            foreach ($data['meta_title'] as $locale => $metaTitle) {
                if (!empty($metaTitle)) {
                    $page->setTranslation('draft_meta_title', $locale, $metaTitle);
                }
            }
        }

        if (isset($data['meta_description'])) {
            foreach ($data['meta_description'] as $locale => $metaDescription) {
                if (!empty($metaDescription)) {
                    $page->setTranslation('draft_meta_description', $locale, $metaDescription);
                }
            }
        }

        // Save draft SEO settings - only set if explicitly provided
        if (array_key_exists('no_index', $data)) {
            $page->draft_no_index = $data['no_index'];
        }

        if (array_key_exists('no_follow', $data)) {
            $page->draft_no_follow = $data['no_follow'];
        }

        if (array_key_exists('no_archive', $data)) {
            $page->draft_no_archive = $data['no_archive'];
        }

        if (array_key_exists('no_snippet', $data)) {
            $page->draft_no_snippet = $data['no_snippet'];
        }

        $page->last_draft_at = now();
        $page->save();

        return $page->refresh();
    }
} 