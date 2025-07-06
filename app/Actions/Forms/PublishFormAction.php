<?php

declare(strict_types=1);

namespace App\Actions\Forms;

use App\Models\Form;

/**
 * Action for publishing form draft data.
 *
 * This action handles publishing draft changes to a form, moving
 * draft data to published fields and clearing draft fields.
 */
class PublishFormAction
{
    /**
     * Execute the action to publish form draft data.
     *
     * @param  Form  $form  The form to publish
     * @return Form The updated form
     */
    public function execute(Form $form): Form
    {
        if ($form->hasDraftChanges()) {
            // Copy draft fields to published fields
            if (! empty($form->draft_name)) {
                $form->name = $form->draft_name;
            }

            if (! empty($form->draft_elements)) {
                $form->elements = $form->draft_elements;
            }

            if (! empty($form->draft_settings)) {
                $form->settings = $form->draft_settings;
            }

            // Clear draft fields
            $form->draft_name = null;
            $form->draft_elements = null;
            $form->draft_settings = null;
            $form->last_draft_at = null;

            $form->save();
        }

        return $form->refresh();
    }
}
