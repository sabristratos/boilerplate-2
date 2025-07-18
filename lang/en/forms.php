<?php

return [
    'title' => 'Forms',
    'new_form' => 'New form',
    'search_placeholder' => 'Search forms...',
    'per_page' => ':count per page',
    'table_name' => 'Name',
    'table_translations' => 'Translations',
    'table_submissions' => 'Submissions',
    'no_forms_found' => 'No forms found.',
    'back_to_forms' => 'Back to forms',
    'form_settings' => 'Form Settings',
    'form_name' => 'Form Name',
    'form_name_help' => 'This is for internal use only and will not be displayed to users.',
    'form_title' => 'Title',
    'form_description' => 'Description',
    'form_success_message' => 'Success Message',
    'form_recipient_email' => 'Recipient Email',
    'form_recipient_email_help' => 'The email address to send form submissions to.',
    'form_send_notification' => 'Send Notification',
    'form_send_notification_help' => 'Send an email notification when a form is submitted.',
    'save_form' => 'Save Form',
    'form_fields' => 'Form Fields',
    'no_fields_yet' => 'This form has no fields yet.',
    'add_field_get_started' => 'Add a field to get started.',
    'edit_field' => 'Edit Field',
    'done' => 'Done',
    'label' => 'Label',
    'placeholder' => 'Placeholder',
    'options' => 'Options',
    'value' => 'Value',
    'add_field' => 'Add Field',
    'add_text_input' => 'Text Input',
    'add_textarea' => 'Add Textarea',
    'add_select' => 'Add Select',
    'add_section' => 'Add Section',
    'edit' => 'Edit',
    'remove' => 'Remove',
    'view' => 'View',
    'submissions_for' => 'Submissions for: :name',
    'table_submitted_at' => 'Submitted At',
    'table_ip_address' => 'IP Address',
    'table_data_preview' => 'Data Preview',
    'no_submissions_found' => 'No submissions found.',
    'submission_details' => 'Submission Details',
    'no_data' => 'No data available',
    'submission_data' => 'Submission Data',
    'submission_metadata' => 'Submission Metadata',
    'submitted_at' => 'Submitted At',
    'ip_address' => 'IP Address',
    'user_agent' => 'User Agent',
    'submission_id' => 'Submission ID',
    'no_submission_data' => 'No Submission Data',
    'no_submission_data_description' => 'This submission does not contain any form data.',
    'close' => 'Close',
    'block_form_label' => 'Form',
    'block_form_placeholder' => 'Select a form',
    'select_form_placeholder' => 'Choose a form to include',
    'toast_form_saved' => 'Form saved successfully.',
    'toast_field_added' => 'Field added successfully.',
    'toast_field_removed' => 'Field removed successfully.',
    'toast_form_created' => 'New form created. Welcome to the builder!',
    'toast_submission_error' => 'An unexpected error occurred. Please try again.',
    'field_type_manager' => [
        'invalid_field_type_class' => 'The provided class is not a valid field type.',
    ],
    'field_types' => [
        'checkbox' => [
            'name' => 'Checkbox',
            'variant_label' => 'Variant',
            'variant_default' => 'Default',
            'variant_cards' => 'Cards',
        ],
        'date' => [
            'name' => 'Date',
        ],
        'email' => [
            'name' => 'Email',
        ],
        'field_type' => [
            'tooltip_label' => 'Tooltip',
        ],
        'file' => [
            'name' => 'File',
        ],
        'number' => [
            'name' => 'Number',
        ],
        'radio' => [
            'variant_label' => 'Variant',
            'variant_default' => 'Default',
            'variant_segmented' => 'Segmented',
            'variant_cards' => 'Cards',
        ],
        'section' => [
            'name' => 'Section',
        ],
        'select' => [
            'searchable_label' => 'Searchable',
        ],
        'text' => [
            'copyable_label' => 'Copyable',
        ],
        'textarea' => [
            'resize_label' => 'Resizable',
            'resize_vertical' => 'Vertical',
            'resize_horizontal' => 'Horizontal',
            'resize_both' => 'Both',
            'resize_none' => 'None',
        ],
        'time' => [
            'name' => 'Time',
        ],
        'submit_button' => [
            'name' => 'Submit Button',
            'alignment_label' => 'Alignment',
            'align_left' => 'Left',
            'align_center' => 'Center',
            'align_right' => 'Right',
            'align_full' => 'Full Width',
        ],
    ],
    'form_builder' => [
        'form_saved_success' => 'Form saved successfully.',
    ],
    'form_index' => [
        'new_form' => 'New Form',
    ],
    'toast_form_deleted' => 'Form deleted successfully.',
    'toast_draft_saved' => 'Draft saved successfully.',
    'toast_form_published' => 'Form published successfully.',
    'toast_draft_discarded' => 'Draft changes discarded.',
    'draft_changes' => 'Draft Changes',
    'desktop' => 'Desktop',
    'tablet' => 'Tablet',
    'mobile' => 'Mobile',
    'no_fields' => 'No fields',
    'add_fields_to_start' => 'Add fields to get started',
    'fields' => 'Fields',
    'settings' => 'Settings',
    'recipient_email' => 'Recipient Email',
    'success_message' => 'Success Message',
    'send_notification_on_submission' => 'Send notification on submission',
    'enable_captcha' => 'Enable Captcha',
    'submit_button' => 'Submit Button',
    'button_text' => 'Button Text',
    'alignment_desktop' => 'Alignment (Desktop)',
    'align_left' => 'Left',
    'align_center' => 'Center',
    'align_right' => 'Right',
    'align_full_width' => 'Full Width',
    'alignment_tablet' => 'Alignment (Tablet)',
    'alignment_mobile' => 'Alignment (Mobile)',
    'general' => 'General',
    'layout' => 'Layout',
    'field_name' => 'Field Name',
    'validation_rules' => 'Validation Rules',
    'add_option' => 'Add Option',
    'submissions_tooltip' => 'View Submissions',
    'delete_form_tooltip' => 'Delete Form',
    'validation' => [
        'required' => 'The :field field is required.',
        'email' => 'The :field must be a valid email address.',
        'numeric' => 'The :field must be a number.',
        'min' => 'The :field must be at least :value characters.',
        'max' => 'The :field may not be greater than :value characters.',
        'min_value' => 'The :field must be at least :value.',
        'max_value' => 'The :field may not be greater than :value.',
        'date' => 'The :field must be a valid date.',
        'date_after' => 'The :field must be a date after :value.',
        'date_before' => 'The :field must be a date before :value.',
        'url' => 'The :field must be a valid URL.',
        'alpha' => 'The :field may only contain letters.',
        'alpha_num' => 'The :field may only contain letters and numbers.',
        'alpha_dash' => 'The :field may only contain letters, numbers, dashes and underscores.',
        'regex' => 'The :field format is invalid.',
        'file' => 'The :field must be a valid file.',
        'image' => 'The :field must be a valid image file.',
        'mimes' => 'The :field must be a file of type: :values.',
        'max_file_size' => 'The :field may not be greater than :value kilobytes.',
        'confirmed' => 'The :field confirmation does not match.',
        'field_required' => 'This field is required.',
        'unexpected_field' => 'Unexpected field.',
        'please_correct_errors' => 'Please correct the errors below.',
    ],
    'errors' => [
        'form_not_found' => 'Form not found.',
        'form_submission_error' => 'An error occurred while submitting the form. Please try again.',
        'failed_to_save_form' => 'Failed to save form. Please try again.',
        'error_rendering_elements' => 'Error rendering form elements: :message',
        'form_has_no_elements' => 'This form has no elements configured yet.',
        'invalid_element_type' => 'Invalid element type: :type',
        'element_not_found' => 'Element with ID :id not found',
        'error_selecting_element' => 'Error selecting element. Please try again.',
        'element_type_required' => 'Element type is required',
        'element_id_required' => 'Element ID is required',
        'element_id_cannot_be_empty' => 'Element ID cannot be empty',
        'no_renderer_found' => 'No renderer found for element type: :type',
        'failed_to_create_element' => 'Failed to create element of type: :type',
        'element_type_cannot_be_empty' => 'Element type cannot be empty',
        'failed_to_save_draft' => 'Failed to save draft. Please try again.',
        'failed_to_publish_form' => 'Failed to publish form. Please try again.',
        'failed_to_discard_draft' => 'Failed to discard draft. Please try again.',
    ],
    'ui' => [
        'form_submitted' => 'Form Submitted!',
        'submit_form' => 'Submit Form',
    ],
    'builder' => [
        'start_building_form' => 'Start Building Your Form',
        'drag_drop_elements' => 'Drag and drop form elements from the toolbox to create your form',
        'select_elements_toolbox' => 'Select elements from the toolbox',
        'drag_here_add_form' => 'Drag them here to add to your form',
        'reorder_elements_dragging' => 'Reorder elements by dragging them',
        'element_not_rendered' => 'Element not rendered',
        'last_updated' => 'Last updated:',
    ],
    'buttons' => [
        'duplicate' => 'Duplicate',
    ],
];
