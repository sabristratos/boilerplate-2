<?php

declare(strict_types=1);

namespace App\Actions\Forms;

use App\Models\Form;

/**
 * Action for discarding form draft data.
 *
 * This action handles discarding all draft changes to a form,
 * clearing all draft fields without affecting published data.
 */
class DiscardFormDraftAction
{
    /**
     * Execute the action to discard form draft data.
     *
     * @param  Form  $form  The form to discard draft data for
     * @return Form The updated form
     */
    public function execute(Form $form): Form
    {
        // Clear draft fields
        $form->draft_name = null;
        $form->draft_elements = null;
        $form->draft_settings = null;
        $form->last_draft_at = null;

        $form->save();

        return $form->refresh();
    }
}
