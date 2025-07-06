<?php

namespace App\Services\FormBuilder\Contracts;

use App\Services\FormBuilder\ElementDTO;

/**
 * Interface for form element renderers.
 */
interface ElementRendererInterface
{
    /**
     * Render the element as HTML.
     */
    public function render(ElementDTO $element): string;

    /**
     * Check if this renderer supports the given element type.
     */
    public function supports(string $type): bool;

    /**
     * Get default properties for this element type.
     */
    public function getDefaultProperties(): array;

    /**
     * Get default styles for this element type.
     */
    public function getDefaultStyles(): array;

    /**
     * Get default validation for this element type.
     */
    public function getDefaultValidation(): array;
}
