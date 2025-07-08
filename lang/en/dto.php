<?php

return [
    /*
    |--------------------------------------------------------------------------
    | DTO Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain validation messages for DTOs.
    | These messages are used when validating Data Transfer Objects.
    |
    */

    'validation' => [
        // General validation messages
        'title_required' => 'The title field is required.',
        'title_required_en' => 'The title field is required in English.',
        'name_required' => 'The name field is required.',
        'name_required_en' => 'The name field is required in English.',
        'slug_required' => 'The slug field is required.',
        'slug_format' => 'The slug must contain only lowercase letters, numbers, and hyphens.',
        'elements_required' => 'The elements field is required.',
        'elements_array' => 'The elements field must be an array.',
        'user_id_required' => 'The user ID field is required.',
        'user_id_integer' => 'The user ID must be a valid integer.',
        'status_required' => 'The status field is required.',
        'status_invalid' => 'The selected status is invalid.',
        'type_required' => 'The type field is required.',
        'type_invalid' => 'The selected type is invalid.',
        'file_required' => 'The file field is required.',
        'file_invalid' => 'The file field must be a valid file.',
        'file_too_large' => 'The file may not be greater than :max kilobytes.',
        'form_id_required' => 'The form ID field is required.',
        'form_id_exists' => 'The selected form does not exist.',
        'data_required' => 'The data field is required.',
        'data_array' => 'The data field must be an array.',
        
        // Form-specific validation messages
        'form_name_required' => 'The form name is required.',
        'form_name_required_en' => 'The form name is required in English.',
        'form_elements_required' => 'The form must have at least one element.',
        'form_element_type_required' => 'Element type is required.',
        'form_element_id_required' => 'Element ID is required.',
        'form_element_properties_required' => 'Element properties are required.',
        'form_element_options_required' => 'Element options are required for select/radio elements.',
        'form_element_option_label_required' => 'Option label is required.',
        'form_element_option_value_required' => 'Option value is required.',
        'form_element_max_size_invalid' => 'Maximum file size must be a positive number.',
        'form_element_min_value_invalid' => 'Minimum value must be a number.',
        'form_element_max_value_invalid' => 'Maximum value must be a number.',
        'form_element_max_length_invalid' => 'Maximum length must be a positive number.',
        
        // Page-specific validation messages
        'page_title_required' => 'The page title is required.',
        'page_title_required_en' => 'The page title is required in English.',
        'page_slug_required' => 'The page slug is required.',
        'page_slug_format' => 'The page slug must contain only lowercase letters, numbers, and hyphens.',
        'page_status_required' => 'The page status is required.',
        'page_status_invalid' => 'The selected page status is invalid.',
        'page_meta_title_invalid' => 'The meta title must be an array.',
        'page_meta_description_invalid' => 'The meta description must be an array.',
        'page_og_image_invalid' => 'The Open Graph image must be a valid URL.',
        'page_twitter_image_invalid' => 'The Twitter image must be a valid URL.',
        'page_canonical_url_invalid' => 'The canonical URL must be a valid URL.',
        'page_twitter_card_type_invalid' => 'The Twitter card type is invalid.',
        
        // User-specific validation messages
        'user_name_required' => 'The user name is required.',
        'user_email_required' => 'The email address is required.',
        'user_email_invalid' => 'The email address must be a valid email.',
        'user_email_unique' => 'This email address is already taken.',
        'user_password_required' => 'The password is required.',
        'user_password_min' => 'The password must be at least :min characters.',
        'user_password_confirmed' => 'The password confirmation does not match.',
        'user_role_invalid' => 'The selected role is invalid.',
        
        // Content block-specific validation messages
        'block_type_required' => 'The block type is required.',
        'block_type_invalid' => 'The selected block type is invalid.',
        'block_data_invalid' => 'The block data must be an array.',
        'block_settings_invalid' => 'The block settings must be an array.',
        'block_order_invalid' => 'The block order must be a non-negative integer.',
        'block_page_id_required' => 'The page ID is required.',
        'block_page_id_exists' => 'The selected page does not exist.',
        'block_visible_invalid' => 'The visibility field must be a boolean.',
        
        // Media-specific validation messages
        'media_file_required' => 'The media file is required.',
        'media_file_invalid' => 'The media file must be a valid file.',
        'media_file_too_large' => 'The media file may not be greater than :max kilobytes.',
        'media_name_invalid' => 'The media name must be a string.',
        'media_alt_text_invalid' => 'The alt text must be a string.',
        'media_caption_invalid' => 'The caption must be a string.',
        'media_collection_invalid' => 'The collection must be a string.',
        'media_disk_invalid' => 'The disk must be a string.',
        'media_conversions_invalid' => 'The conversions must be an array.',
        'media_custom_properties_invalid' => 'The custom properties must be an array.',
        
        // Form submission-specific validation messages
        'submission_form_id_required' => 'The form ID is required.',
        'submission_form_id_exists' => 'The selected form does not exist.',
        'submission_data_required' => 'The submission data is required.',
        'submission_data_invalid' => 'The submission data must be an array.',
        'submission_ip_invalid' => 'The IP address must be a valid IP address.',
        'submission_user_agent_invalid' => 'The user agent must be a string.',
        'submission_data_invalid' => 'The submission data must be an array.',
        'submission_contains_sensitive_data' => 'The submission contains sensitive data that cannot be processed.',
        'media_mime_type_invalid' => 'The MIME type format is invalid.',
        
        // Translatable field validation messages
        'translatable_field_required' => 'At least one translation is required.',
        'translatable_field_string' => 'The :field (:locale) must be a string.',
        'translatable_field_max' => 'The :field (:locale) may not be greater than :max characters.',
        
        // Element-specific validation messages
        'element_id_required' => 'Element ID is required.',
        'element_type_required' => 'Element type is required.',
        'element_type_invalid' => 'The selected element type is invalid.',
        'element_properties_required' => 'Element properties are required.',
        'element_properties_array' => 'Element properties must be an array.',
        'element_validation_array' => 'Element validation must be an array.',
        'element_options_required' => 'Element options are required.',
        'element_options_array' => 'Element options must be an array.',
        'element_options_min' => 'Element must have at least one option.',
        'element_option_label_required' => 'Option label is required.',
        'element_option_value_required' => 'Option value is required.',
        'element_max_size_invalid' => 'Maximum file size must be a positive number.',
        'element_min_value_invalid' => 'Minimum value must be a number.',
        'element_max_value_invalid' => 'Maximum value must be a number.',
        'element_max_length_invalid' => 'Maximum length must be a positive number.',
    ],

    /*
    |--------------------------------------------------------------------------
    | DTO Attribute Names
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap attribute placeholders
    | with more reader-friendly names for validation messages.
    |
    */

    'attributes' => [
        // General attributes
        'title' => 'title',
        'name' => 'name',
        'slug' => 'slug',
        'elements' => 'elements',
        'user_id' => 'user ID',
        'status' => 'status',
        'type' => 'type',
        'file' => 'file',
        'form_id' => 'form ID',
        'data' => 'data',
        
        // Form-specific attributes
        'form_name' => 'form name',
        'form_elements' => 'form elements',
        'form_element' => 'form element',
        'form_element_id' => 'element ID',
        'form_element_type' => 'element type',
        'form_element_properties' => 'element properties',
        'form_element_options' => 'element options',
        'form_element_option' => 'element option',
        'form_element_option_label' => 'option label',
        'form_element_option_value' => 'option value',
        'form_element_max_size' => 'maximum file size',
        'form_element_min_value' => 'minimum value',
        'form_element_max_value' => 'maximum value',
        'form_element_max_length' => 'maximum length',
        
        // Page-specific attributes
        'page_title' => 'page title',
        'page_slug' => 'page slug',
        'page_status' => 'page status',
        'page_meta_title' => 'meta title',
        'page_meta_description' => 'meta description',
        'page_meta_keywords' => 'meta keywords',
        'page_og_title' => 'Open Graph title',
        'page_og_description' => 'Open Graph description',
        'page_og_image' => 'Open Graph image',
        'page_twitter_title' => 'Twitter title',
        'page_twitter_description' => 'Twitter description',
        'page_twitter_image' => 'Twitter image',
        'page_twitter_card_type' => 'Twitter card type',
        'page_canonical_url' => 'canonical URL',
        'page_structured_data' => 'structured data',
        'page_no_index' => 'no index',
        'page_no_follow' => 'no follow',
        'page_no_archive' => 'no archive',
        'page_no_snippet' => 'no snippet',
        
        // User-specific attributes
        'user_name' => 'user name',
        'user_email' => 'email address',
        'user_password' => 'password',
        'user_password_confirmation' => 'password confirmation',
        'user_role' => 'user role',
        
        // Content block-specific attributes
        'block_type' => 'block type',
        'block_data' => 'block data',
        'block_settings' => 'block settings',
        'block_order' => 'block order',
        'block_page_id' => 'page ID',
        'block_visible' => 'visibility',
        
        // Media-specific attributes
        'media_file' => 'media file',
        'media_name' => 'media name',
        'media_alt_text' => 'alt text',
        'media_caption' => 'caption',
        'media_collection' => 'collection',
        'media_disk' => 'disk',
        'media_conversions' => 'conversions',
        'media_custom_properties' => 'custom properties',
        
        // Form submission-specific attributes
        'submission_form_id' => 'form ID',
        'submission_data' => 'submission data',
        'submission_ip' => 'IP address',
        'submission_user_agent' => 'user agent',
        
        // Element-specific attributes
        'element_id' => 'element ID',
        'element_type' => 'element type',
        'element_properties' => 'element properties',
        'element_validation' => 'element validation',
        'element_options' => 'element options',
        'element_option' => 'element option',
        'element_option_label' => 'option label',
        'element_option_value' => 'option value',
        'element_max_size' => 'maximum file size',
        'element_min_value' => 'minimum value',
        'element_max_value' => 'maximum value',
        'element_max_length' => 'maximum length',
    ],

    /*
    |--------------------------------------------------------------------------
    | DTO Error Messages
    |--------------------------------------------------------------------------
    |
    | The following language lines contain error messages for DTO operations.
    |
    */

    'errors' => [
        'validation_failed' => 'Validation failed. Please check the errors below.',
        'invalid_dto_type' => 'Invalid DTO type provided.',
        'missing_required_fields' => 'Required fields are missing.',
        'invalid_data_structure' => 'Invalid data structure provided.',
        'translation_missing' => 'Required translation is missing.',
        'element_validation_failed' => 'Element validation failed.',
        'file_upload_failed' => 'File upload failed.',
        'database_operation_failed' => 'Database operation failed.',
        'permission_denied' => 'Permission denied.',
        'resource_not_found' => 'Resource not found.',
        'duplicate_entry' => 'Duplicate entry detected.',
        'invalid_format' => 'Invalid format provided.',
        'size_limit_exceeded' => 'Size limit exceeded.',
        'unsupported_type' => 'Unsupported type provided.',
    ],
]; 