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
     *
     * @param ElementDTO $element
     * @return string
     */
    public function render(ElementDTO $element): string;

    /**
     * Check if this renderer supports the given element type.
     *
     * @param string $type
     * @return bool
     */
    public function supports(string $type): bool;

    /**
     * Get default properties for this element type.
     *
     * @return array
     */
    public function getDefaultProperties(): array;

    /**
     * Get default styles for this element type.
     *
     * @return array
     */
    public function getDefaultStyles(): array;

    /**
     * Get default validation for this element type.
     *
     * @return array
     */
    public function getDefaultValidation(): array;
}
