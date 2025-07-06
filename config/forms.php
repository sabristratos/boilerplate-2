<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Form Model
    |--------------------------------------------------------------------------
    |
    | This is the model that will be used by the form builder.
    |
    */
    'form_model' => \App\Models\Form::class,

    /*
    |--------------------------------------------------------------------------
    | Form Builder Settings
    |--------------------------------------------------------------------------
    |
    | Default settings for the form builder interface and behavior.
    |
    */
    'builder' => [
        'default_settings' => [
            'backgroundColor' => '#ffffff',
            'defaultFont' => 'system-ui',
        ],
        'default_tab' => 'toolbox',
        'default_breakpoint' => 'desktop',
    ],

    /*
    |--------------------------------------------------------------------------
    | Breakpoints Configuration
    |--------------------------------------------------------------------------
    |
    | Responsive breakpoints for the form builder.
    |
    */
    'breakpoints' => [
        'desktop' => [
            'name' => 'Desktop',
            'icon' => 'computer-desktop',
            'max_width' => 'max-w-full',
            'description' => 'Large screens (1024px and above)',
            'min_width' => 1024,
        ],
        'tablet' => [
            'name' => 'Tablet',
            'icon' => 'device-tablet',
            'max_width' => 'max-w-3xl',
            'description' => 'Medium tablets (768px - 1024px)',
            'min_width' => 768,
            'max_width_px' => 1023,
        ],
        'mobile' => [
            'name' => 'Mobile',
            'icon' => 'device-phone-mobile',
            'max_width' => 'max-w-sm',
            'description' => 'Phones and small tablets (up to 768px)',
            'max_width_px' => 767,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Grid System Configuration
    |--------------------------------------------------------------------------
    |
    | Grid system settings for responsive layouts.
    |
    */
    'grid' => [
        'columns' => 12,
        'widths' => [
            'full' => [
                'label' => 'Full Width',
                'columns' => 12,
                'description' => 'Takes up the full width (12 columns)',
            ],
            '1/2' => [
                'label' => 'Half Width (1/2)',
                'columns' => 6,
                'description' => 'Takes up half the width (6 columns)',
            ],
            '1/3' => [
                'label' => 'One Third (1/3)',
                'columns' => 4,
                'description' => 'Takes up one-third width (4 columns)',
            ],
            '2/3' => [
                'label' => 'Two Thirds (2/3)',
                'columns' => 8,
                'description' => 'Takes up two-thirds width (8 columns)',
            ],
            '1/4' => [
                'label' => 'Quarter (1/4)',
                'columns' => 3,
                'description' => 'Takes up quarter width (3 columns)',
            ],
            '3/4' => [
                'label' => 'Three Quarters (3/4)',
                'columns' => 9,
                'description' => 'Takes up three-quarters width (9 columns)',
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Validation Rules Configuration
    |--------------------------------------------------------------------------
    |
    | Available validation rules and their configurations.
    |
    */
    'validation' => [
        'rules' => [
            'required' => [
                'label' => 'Required',
                'description' => 'Field must be filled out',
                'rule' => 'required',
                'icon' => 'exclamation-triangle',
                'has_value' => false,
                'category' => 'Basic',
            ],
            'email' => [
                'label' => 'Email',
                'description' => 'Must be a valid email address',
                'rule' => 'email',
                'icon' => 'envelope',
                'has_value' => false,
                'category' => 'Format',
            ],
            'numeric' => [
                'label' => 'Numeric',
                'description' => 'Must contain only numbers',
                'rule' => 'numeric',
                'icon' => 'calculator',
                'has_value' => false,
                'category' => 'Format',
            ],
            'min' => [
                'label' => 'Minimum Length',
                'description' => 'Must be at least X characters',
                'rule' => 'min',
                'icon' => 'arrow-down',
                'has_value' => true,
                'category' => 'Length',
            ],
            'max' => [
                'label' => 'Maximum Length',
                'description' => 'Must be no more than X characters',
                'rule' => 'max',
                'icon' => 'arrow-up',
                'has_value' => true,
                'category' => 'Length',
            ],
            'min_value' => [
                'label' => 'Minimum Value',
                'description' => 'Must be at least X (for numbers)',
                'rule' => 'min',
                'icon' => 'arrow-down',
                'has_value' => true,
                'category' => 'Range',
            ],
            'max_value' => [
                'label' => 'Maximum Value',
                'description' => 'Must be no more than X (for numbers)',
                'rule' => 'max',
                'icon' => 'arrow-up',
                'has_value' => true,
                'category' => 'Range',
            ],
            'date' => [
                'label' => 'Valid Date',
                'description' => 'Must be a valid date',
                'rule' => 'date',
                'icon' => 'calendar',
                'has_value' => false,
                'category' => 'Format',
            ],
            'date_after' => [
                'label' => 'Date After',
                'description' => 'Must be after a specific date',
                'rule' => 'date_after',
                'icon' => 'calendar-days',
                'has_value' => true,
                'category' => 'Date Range',
            ],
            'date_before' => [
                'label' => 'Date Before',
                'description' => 'Must be before a specific date',
                'rule' => 'date_before',
                'icon' => 'calendar-days',
                'has_value' => true,
                'category' => 'Date Range',
            ],
            'url' => [
                'label' => 'URL',
                'description' => 'Must be a valid URL',
                'rule' => 'url',
                'icon' => 'link',
                'has_value' => false,
                'category' => 'Format',
            ],
            'alpha' => [
                'label' => 'Letters Only',
                'description' => 'Must contain only letters',
                'rule' => 'alpha',
                'icon' => 'document-text',
                'has_value' => false,
                'category' => 'Format',
            ],
            'alpha_num' => [
                'label' => 'Letters & Numbers',
                'description' => 'Must contain only letters and numbers',
                'rule' => 'alpha_num',
                'icon' => 'hashtag',
                'has_value' => false,
                'category' => 'Format',
            ],
            'regex' => [
                'label' => 'Custom Pattern',
                'description' => 'Must match a specific pattern',
                'rule' => 'regex',
                'icon' => 'code-bracket',
                'has_value' => true,
                'category' => 'Advanced',
            ],
            'file' => [
                'label' => 'Valid File',
                'description' => 'Must be a valid file upload',
                'rule' => 'file',
                'icon' => 'document',
                'has_value' => false,
                'category' => 'File',
            ],
            'image' => [
                'label' => 'Image File',
                'description' => 'Must be a valid image file',
                'rule' => 'image',
                'icon' => 'photo',
                'has_value' => false,
                'category' => 'File',
            ],
            'mimes' => [
                'label' => 'File Type',
                'description' => 'Must be one of the specified file types',
                'rule' => 'mimes',
                'icon' => 'document-text',
                'has_value' => true,
                'category' => 'File',
            ],
            'max_file_size' => [
                'label' => 'Maximum File Size',
                'description' => 'File size must not exceed X KB/MB',
                'rule' => 'max',
                'icon' => 'arrow-up',
                'has_value' => true,
                'category' => 'File',
            ],
            'confirmed' => [
                'label' => 'Password Confirmation',
                'description' => 'Must match the password confirmation field',
                'rule' => 'confirmed',
                'icon' => 'check-circle',
                'has_value' => false,
                'category' => 'Security',
            ],
        ],
        'default_messages' => [
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
            'regex' => 'The :field format is invalid.',
            'file' => 'The :field must be a valid file.',
            'image' => 'The :field must be a valid image file.',
            'mimes' => 'The :field must be a file of type: :values.',
            'max_file_size' => 'The :field may not be greater than :value kilobytes.',
            'confirmed' => 'The :field confirmation does not match.',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Icons Configuration
    |--------------------------------------------------------------------------
    |
    | Available icons for form elements.
    |
    */
    'icons' => [
        'available' => [
            'user' => 'User',
            'envelope' => 'Email',
            'phone' => 'Phone',
            'map-pin' => 'Location',
            'calendar' => 'Calendar',
            'calendar-days' => 'Calendar Days',
            'clock' => 'Time',
            'lock-closed' => 'Password',
            'magnifying-glass' => 'Search',
            'cog-6-tooth' => 'Settings',
            'star' => 'Rating',
            'heart' => 'Favorite',
            'bookmark' => 'Bookmark',
            'document' => 'Document',
            'document-text' => 'Document Text',
            'folder' => 'Folder',
            'photo' => 'Photo',
            'exclamation-triangle' => 'Warning',
            'check-circle' => 'Success',
            'x-circle' => 'Error',
            'information-circle' => 'Info',
            'question-mark-circle' => 'Help',
            'calculator' => 'Calculator',
            'arrow-down' => 'Arrow Down',
            'arrow-up' => 'Arrow Up',
            'arrow-left' => 'Arrow Left',
            'arrow-right' => 'Arrow Right',
            'chevron-down' => 'Chevron Down',
            'chevron-up' => 'Chevron Up',
            'chevron-left' => 'Chevron Left',
            'chevron-right' => 'Chevron Right',
            'plus' => 'Plus',
            'minus' => 'Minus',
            'x-mark' => 'Close',
            'bars-3' => 'Menu',
            'eye' => 'View',
            'eye-slash' => 'Hide',
            'pencil-square' => 'Edit',
            'trash' => 'Delete',
            'sparkles' => 'Sparkles',
            'list-bullet' => 'List',
            'squares-2x2' => 'Grid',
            'hashtag' => 'Hashtag',
            'shield-check' => 'Security',
            'cursor-arrow-rays' => 'Cursor',
            'arrows-pointing-out' => 'Expand',
            'plus-circle' => 'Add',
        ],
        'categories' => [
            'User Interface' => [
                'user', 'envelope', 'phone', 'map-pin', 'calendar', 'calendar-days', 'clock',
            ],
            'Actions' => [
                'magnifying-glass', 'plus', 'minus', 'x-mark', 'pencil-square', 'trash',
            ],
            'Navigation' => [
                'arrow-down', 'arrow-up', 'arrow-left', 'arrow-right', 'chevron-down', 'chevron-up', 'chevron-left', 'chevron-right',
            ],
            'Security & Settings' => [
                'lock-closed', 'cog-6-tooth', 'shield-check',
            ],
            'Content' => [
                'document', 'document-text', 'folder', 'photo', 'list-bullet', 'squares-2x2',
            ],
            'Feedback' => [
                'exclamation-triangle', 'check-circle', 'x-circle', 'information-circle', 'question-mark-circle', 'star',
            ],
            'UI Elements' => [
                'eye', 'eye-slash', 'bars-3', 'sparkles', 'cursor-arrow-rays', 'arrows-pointing-out', 'plus-circle',
            ],
            'Data & Numbers' => [
                'calculator', 'hashtag',
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Element Default Properties
    |--------------------------------------------------------------------------
    |
    | Default properties for different element types.
    |
    */
    'elements' => [
        'default_properties' => [
            'fluxProps' => [
                'clearable' => false,
                'copyable' => false,
                'viewable' => false,
                'icon' => '',
                'iconTrailing' => '',
                'variant' => 'default',
                'searchable' => false,
                'multiple' => false,
            ],
        ],
        'default_styles' => [
            'desktop' => [
                'width' => 'full',
                'fontSize' => '',
            ],
            'tablet' => [
                'width' => 'full',
                'fontSize' => '',
            ],
            'mobile' => [
                'width' => 'full',
                'fontSize' => '',
            ],
        ],
        'default_validation' => [
            'rules' => [],
            'messages' => [],
            'values' => [],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting Configuration
    |--------------------------------------------------------------------------
    |
    | Rate limiting settings for form submissions.
    |
    */
    'rate_limiting' => [
        'max_submissions_per_hour' => 10,
        'max_submissions_per_day' => 50,
        'enable_rate_limiting' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | UI Configuration
    |--------------------------------------------------------------------------
    |
    | UI-related configurations for the form builder.
    |
    */
    'ui' => [
        'sortable' => [
            'animation' => 150,
            'ghost_class' => 'sortable-ghost',
        ],
        'tooltips' => [
            'save' => 'Save your form changes',
            'preview' => 'Preview the form as users will see it',
            'drag_handle' => 'Drag to reorder this element',
            'delete' => 'Delete this element',
            'width_select' => 'Choose how much horizontal space this element takes up in the 12-column grid system',
            'font_size' => 'Set a custom font size for this element. Use CSS units like px, rem, em, or %',
            'mobile_breakpoint' => 'Mobile breakpoint (up to 768px)',
            'tablet_breakpoint' => 'Tablet breakpoint (768px - 1024px)',
            'desktop_breakpoint' => 'Desktop breakpoint (1024px and above)',
        ],
    ],
];
