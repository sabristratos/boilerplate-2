<?php

namespace App\Services\FormBuilder\PrebuiltForms;

class ContactForm implements PrebuiltFormInterface
{
    public function getName(): string
    {
        return 'Contact Form';
    }

    public function getDescription(): string
    {
        return 'A simple contact form with name, email, and message fields.';
    }

    public function getElements(): array
    {
        return [
            [
                'type' => 'text',
                'properties' => [
                    'label' => 'Name',
                    'placeholder' => 'Your Name',
                ],
                'styles' => [
                    'desktop' => ['width' => 'full'],
                    'tablet' => ['width' => 'full'],
                    'mobile' => ['width' => 'full'],
                ],
                'validation' => [
                    'rules' => ['required'],
                ],
            ],
            [
                'type' => 'email',
                'properties' => [
                    'label' => 'Email',
                    'placeholder' => 'you@example.com',
                ],
                'styles' => [
                    'desktop' => ['width' => 'full'],
                    'tablet' => ['width' => 'full'],
                    'mobile' => ['width' => 'full'],
                ],
                'validation' => [
                    'rules' => ['required', 'email'],
                ],
            ],
            [
                'type' => 'textarea',
                'properties' => [
                    'label' => 'Message',
                    'placeholder' => 'How can we help you?',
                ],
                'styles' => [
                    'desktop' => ['width' => 'full'],
                    'tablet' => ['width' => 'full'],
                    'mobile' => ['width' => 'full'],
                ],
                'validation' => [
                    'rules' => ['required'],
                ],
            ],
        ];
    }

    public function getSettings(): array
    {
        return [
            'backgroundColor' => '#ffffff',
            'defaultFont' => 'system-ui',
        ];
    }
} 