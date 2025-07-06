<?php

namespace App\Blocks;

class ContactBlock extends Block
{
    public function getName(): string
    {
        return 'Contact Form';
    }

    public function getDescription(): string
    {
        return 'Display a contact form with customizable styling and form selection.';
    }

    public function getCategory(): string
    {
        return 'forms';
    }

    public function getTags(): array
    {
        return ['contact', 'form', 'lead', 'conversion'];
    }

    public function getComplexity(): string
    {
        return 'basic';
    }

    public function getIcon(): string
    {
        return 'envelope';
    }

    public function getDefaultData(): array
    {
        return [
            'heading' => 'Get in Touch',
            'subheading' => 'We\'d love to hear from you. Send us a message and we\'ll respond as soon as possible.',
            'form_id' => null,
            'background_color' => 'white',
            'text_alignment' => 'center',
            'show_contact_info' => true,
            'contact_info' => [
                'email' => 'hello@example.com',
                'phone' => '+1 (555) 123-4567',
                'address' => '123 Main St, City, State 12345',
            ],
        ];
    }

    public function getTranslatableFields(): array
    {
        return ['heading', 'subheading', 'contact_info'];
    }

    public function validationRules(): array
    {
        return [
            'heading' => 'nullable|string|max:255',
            'subheading' => 'nullable|string|max:500',
            'form_id' => 'nullable|exists:forms,id',
            'background_color' => 'required|string|in:white,gray,primary,secondary',
            'text_alignment' => 'required|string|in:left,center,right',
            'show_contact_info' => 'boolean',
            'contact_info' => 'nullable|array',
            'contact_info.email' => 'nullable|email',
            'contact_info.phone' => 'nullable|string|max:255',
            'contact_info.address' => 'nullable|string|max:500',
        ];
    }
} 