<?php

declare(strict_types=1);

namespace App\Services\FormBuilder\PrebuiltForms;

class PrebuiltFormRegistry
{
    /**
     * @return PrebuiltFormInterface[]
     */
    public static function all(): array
    {
        return [
            new ContactForm,
            // Add new prebuilt forms here
        ];
    }

    public static function find(string $class): ?PrebuiltFormInterface
    {
        foreach (self::all() as $form) {
            if (get_class($form) === $class) {
                return $form;
            }
        }

        return null;
    }
}
