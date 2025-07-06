<?php

declare(strict_types=1);

namespace App\Actions\Forms;

use App\Models\Form;

/**
 * Action for saving draft form data.
 *
 * This action handles saving draft changes to a form, including
 * elements, settings, and name translations.
 */
class SaveDraftFormAction
{
    /**
     * Execute the action to save draft form data.
     *
     * @param  Form  $form  The form to save draft data for
     * @param  array  $elements  The draft elements array
     * @param  array  $settings  The draft settings array
     * @param  array  $name  The draft name translations array
     * @param  string  $locale  The current locale
     * @return Form The updated form
     */
    public function execute(Form $form, array $elements, array $settings, array $name, string $locale): Form
    {
        // Save draft elements
        $form->draft_elements = $elements;

        // Save draft settings
        $form->draft_settings = $settings;

        // Save draft name translations
        if (! empty($name)) {
            foreach ($name as $lang => $translation) {
                if (! empty($translation)) {
                    $form->setTranslation('draft_name', $lang, $translation);
                }
            }
        }

        // Update last draft timestamp
        $form->last_draft_at = now();

        $form->save();

        return $form->refresh();
    }
}
