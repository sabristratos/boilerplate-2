<?php

return [
    'welcome_message' => 'Welcome to your admin panel! This guide will help you understand and use all the features available to manage your website.',

    'quick_start' => [
        'title' => 'Quick Start Guide',
        'description' => 'New to the admin panel? Start here to get familiar with the basics:',
        'dashboard' => [
            'title' => 'Dashboard Overview',
            'description' => 'Your dashboard shows key statistics and quick access to important features. Check here regularly to monitor your website\'s activity.',
        ],
        'content_management' => [
            'title' => 'Content Management',
            'description' => 'Create and edit pages, manage forms, and organize your media files. This is where you\'ll spend most of your time.',
        ],
        'pro_tip' => [
            'title' => 'Pro Tip',
            'description' => 'Use the search bar at the top of the sidebar to quickly find any page or feature. You can also use keyboard shortcuts like Ctrl+F (Cmd+F on Mac) to search within pages.',
        ],
    ],

    'content_management' => [
        'title' => 'Content Management',
        'pages' => [
            'title' => 'Pages',
            'description' => 'Create and edit website pages using our visual page builder.',
            'how_to_create' => [
                'title' => 'How to create a page:',
                'steps' => [
                    'Go to Pages in the sidebar',
                    'Click "Create New Page"',
                    'Add a title and content',
                    'Use the page builder to add sections',
                    'Save and publish when ready',
                ],
            ],
        ],
        'forms' => [
            'title' => 'Forms',
            'description' => 'Build custom forms to collect information from your visitors.',
            'features' => [
                'title' => 'Form Builder Features:',
                'items' => [
                    'Drag and drop form elements',
                    'Multiple field types (text, email, file upload, etc.)',
                    'Custom validation rules',
                    'Preview mode to test forms',
                    'View and export submissions',
                ],
            ],
        ],
        'media_library' => [
            'title' => 'Media Library',
            'description' => 'Organize and manage all your images, documents, and other files.',
            'tips' => [
                'title' => 'Media Management Tips:',
                'items' => [
                    'Upload files by dragging them or clicking upload',
                    'Add media from URLs for external files',
                    'Search and filter by file type',
                    'Images are automatically optimized',
                    'Use collections to organize files',
                ],
            ],
        ],
    ],

    'platform_tools' => [
        'title' => 'Platform Tools',
        'settings' => [
            'title' => 'Settings',
            'description' => 'Configure your website\'s appearance, email settings, and more.',
            'categories' => [
                'title' => 'Key Settings Categories:',
                'items' => [
                    'General: Site name, URL, languages',
                    'Appearance: Theme, colors, logo',
                    'Email: SMTP settings, notifications',
                    'Security: Password policies, 2FA',
                    'SEO: Meta tags, sitemap settings',
                ],
            ],
        ],
        'translations' => [
            'title' => 'Translations',
            'description' => 'Manage multiple languages for your website content.',
            'features' => [
                'title' => 'Translation Features:',
                'items' => [
                    'Add new languages easily',
                    'Translate interface text',
                    'Manage content translations',
                    'Set default and fallback languages',
                    'Export/import translation files',
                ],
            ],
        ],
        'database_backup' => [
            'title' => 'Database Backup',
            'description' => 'Create backups of your website data for safety.',
            'best_practices' => [
                'title' => 'Backup Best Practices:',
                'items' => [
                    'Create regular backups (weekly recommended)',
                    'Download backups to your computer',
                    'Test backup restoration periodically',
                    'Keep multiple backup versions',
                    'Store backups in a secure location',
                ],
            ],
        ],
    ],

    'tutorials' => [
        'title' => 'Step-by-Step Tutorials',
        'create_first_page' => [
            'title' => 'How to Create Your First Page',
            'steps' => [
                'Navigate to Pages in the sidebar menu',
                'Click the "Create New Page" button',
                'Fill in the basic information:',
                'Use the page builder to add content sections',
                'Click "Save Draft" to save your work, or "Publish" to make it live',
            ],
            'basic_info' => [
                'Page title (this will appear in the browser tab)',
                'URL slug (the web address for your page)',
                'Meta description (for search engines)',
            ],
        ],
        'build_contact_form' => [
            'title' => 'Building a Contact Form',
            'steps' => [
                'Go to Forms in the sidebar',
                'Click "Create New Form"',
                'Use the form builder to add fields:',
                'Configure validation rules (required fields, email format, etc.)',
                'Test your form using the preview mode',
                'Save and embed the form on your website',
            ],
            'fields' => [
                'Text input for name',
                'Email input for email address',
                'Textarea for message',
                'Submit button',
            ],
        ],
        'manage_media_library' => [
            'title' => 'Managing Your Media Library',
            'steps' => [
                'Access the Media Library from the sidebar',
                'Upload files by:',
                'Organize files using collections (folders)',
                'Search and filter files by name, type, or date',
                'Use files in your pages and forms by selecting them from the media picker',
            ],
            'upload_methods' => [
                'Dragging files directly into the upload area',
                'Clicking "Upload" and selecting files',
                'Adding files from a URL',
            ],
        ],
        'configure_settings' => [
            'title' => 'Configuring Website Settings',
            'steps' => [
                'Go to Settings in the sidebar',
                'Start with General settings:',
                'Customize Appearance:',
                'Configure Email settings for notifications',
                'Set up SEO settings for better search engine visibility',
            ],
            'general_settings' => [
                'Set your website name and URL',
                'Choose your default language',
                'Set timezone and date format',
            ],
            'appearance_settings' => [
                'Upload your logo and favicon',
                'Choose your brand colors',
                'Select a theme (light/dark mode)',
            ],
        ],
    ],

    'common_tasks' => [
        'title' => 'Common Tasks',
        'quick_actions' => [
            'title' => 'Quick Actions',
            'add_page' => 'Add a new page: Pages → Create New Page',
            'create_form' => 'Create a form: Forms → Create New Form',
            'upload_media' => 'Upload media: Media Library → Upload button',
            'change_settings' => 'Change settings: Settings → Select category',
        ],
        'troubleshooting' => [
            'title' => 'Troubleshooting',
            'page_not_saving' => 'Page not saving? Check that all required fields are filled',
            'form_not_working' => 'Form not working? Verify email settings are configured',
            'images_not_loading' => 'Images not loading? Check file size (max 10MB) and format',
            'cant_login' => 'Can\'t log in? Try resetting your password',
        ],
    ],

    'best_practices' => [
        'title' => 'Tips and Best Practices',
        'security' => [
            'title' => 'Security',
            'items' => [
                'Use strong, unique passwords',
                'Enable two-factor authentication',
                'Regularly update your login credentials',
                'Create regular backups of your data',
            ],
        ],
        'performance' => [
            'title' => 'Performance',
            'items' => [
                'Optimize images before uploading',
                'Use descriptive file names',
                'Keep pages focused and concise',
                'Test forms before publishing',
            ],
        ],
        'seo' => [
            'title' => 'SEO',
            'items' => [
                'Write descriptive page titles',
                'Add meta descriptions to all pages',
                'Use alt text for images',
                'Create a logical site structure',
            ],
        ],
    ],

    'keyboard_shortcuts' => [
        'title' => 'Keyboard Shortcuts',
        'general' => [
            'title' => 'General Shortcuts',
            'save' => 'Save current page/form',
            'search' => 'Search within page',
            'undo' => 'Undo last action',
            'redo' => 'Redo last action',
        ],
        'navigation' => [
            'title' => 'Navigation Shortcuts',
            'help' => 'Go to Help page',
            'dashboard' => 'Go to Dashboard',
            'pages' => 'Go to Pages',
            'media' => 'Go to Media Library',
        ],
    ],

    'support' => [
        'title' => 'Need More Help?',
        'questions' => [
            'title' => 'Still Have Questions?',
            'description' => 'If you can\'t find what you\'re looking for in this help guide, don\'t hesitate to reach out for support. Our team is here to help you succeed with your website.',
        ],
        'contact' => [
            'title' => 'Contact Support',
            'email' => 'Email: :email',
            'response_time' => 'Response time: Within 24 hours',
            'include_screenshots' => 'Include screenshots for faster help',
            'mention_username' => 'Mention your admin username',
        ],
        'pro_tip' => [
            'title' => 'Pro Tip',
            'description' => 'Before contacting support, try using the search function in this help page or check if your question is answered in the step-by-step tutorials above. Most common issues can be resolved quickly!',
        ],
    ],
];
