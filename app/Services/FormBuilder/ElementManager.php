<?php

declare(strict_types=1);

namespace App\Services\FormBuilder;

/**
 * Manages operations on form elements (add, update, delete, reorder, etc.).
 *
 * @property ElementFactory $factory
 *
 * @method void addElement(array &$elements, string $type)
 * @method void updateElement(array &$elements, string $elementId, array $updates)
 * @method void deleteElement(array &$elements, string $elementId)
 * @method void reorderElements(array &$elements, array $orderedOrders)
 * @method void updateElementWidth(array &$elements, string $elementId, string $breakpoint, string $width)
 * @method int|null findElementIndex(array $elements, string $elementId)
 * @method array|null findElement(array $elements, string $elementId)
 */
class ElementManager
{
    private ElementFactory $factory;

    /**
     * ElementManager constructor.
     */
    public function __construct(ElementFactory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * Add a new element to the elements array.
     *
     * @param array $elements Reference to the elements array
     * @param string $type The element type
     * @return void
     * @throws \InvalidArgumentException If the type is invalid
     */
    public function addElement(array &$elements, string $type): void
    {
        if (empty($type)) {
            throw new \InvalidArgumentException(__('forms.errors.element_type_cannot_be_empty'));
        }

        $newElement = $this->factory->createElement($type);
        
        if (! $newElement) {
            throw new \InvalidArgumentException(__('forms.errors.failed_to_create_element', ['type' => $type]));
        }

        $newElement->order = count($elements);
        $elements[] = $newElement->toArray();
    }

    /**
     * Update an existing element.
     *
     * @param array $elements Reference to the elements array
     * @param string $elementId The element ID
     * @param array $updates The updates to apply
     * @return void
     * @throws \InvalidArgumentException If the element ID is invalid or not found
     */
    public function updateElement(array &$elements, string $elementId, array $updates): void
    {
        if (empty($elementId)) {
            throw new \InvalidArgumentException(__('forms.errors.element_id_cannot_be_empty'));
        }

        $index = $this->findElementIndex($elements, $elementId);
        if ($index === null) {
            throw new \InvalidArgumentException(__('forms.errors.element_not_found', ['id' => $elementId]));
        }

        $elements[$index] = array_merge($elements[$index], $updates);
    }

    /**
     * Delete an element from the elements array.
     *
     * @param array $elements Reference to the elements array
     * @param string $elementId The element ID
     * @return void
     * @throws \InvalidArgumentException If the element ID is invalid or not found
     */
    public function deleteElement(array &$elements, string $elementId): void
    {
        if (empty($elementId)) {
            throw new \InvalidArgumentException('Element ID cannot be empty');
        }

        $originalCount = count($elements);
        $elements = array_filter($elements, fn ($element) => $element['id'] !== $elementId);
        $elements = array_values($elements); // Re-index array

        if (count($elements) === $originalCount) {
            throw new \InvalidArgumentException(__('forms.errors.element_not_found', ['id' => $elementId]));
        }
    }

    /**
     * Reorder elements based on new order.
     *
     * @param array $elements Reference to the elements array
     * @param array $orderedOrders The new order of element orders
     * @return void
     */
    public function reorderElements(array &$elements, array $orderedOrders): void
    {
        $elements = collect($elements)
            ->sortBy(function ($element) use ($orderedOrders) {
                return array_search($element['order'], $orderedOrders);
            })
            ->values()
            ->map(function ($element, $index) {
                $element['order'] = $index;

                return $element;
            })
            ->all();
    }

    /**
     * Update element width for a specific breakpoint.
     *
     * @param array $elements Reference to the elements array
     * @param string $elementId The element ID
     * @param string $breakpoint The breakpoint name
     * @param string $width The width value
     * @return void
     */
    public function updateElementWidth(array &$elements, string $elementId, string $breakpoint, string $width): void
    {
        $index = $this->findElementIndex($elements, $elementId);

        if ($index !== null) {
            // Ensure the styles structure exists
            if (! isset($elements[$index]['styles'])) {
                $elements[$index]['styles'] = $this->getDefaultStyles();
            }

            // Ensure the breakpoint structure exists
            if (! isset($elements[$index]['styles'][$breakpoint])) {
                $elements[$index]['styles'][$breakpoint] = ['width' => 'full', 'fontSize' => ''];
            }

            // Update the width
            $elements[$index]['styles'][$breakpoint]['width'] = $width;
        }
    }

    /**
     * Duplicate an element by its ID and append the copy to the elements array.
     *
     * @param array $elements Reference to the elements array
     * @param string $elementId The element ID to duplicate
     * @return void
     * @throws \InvalidArgumentException If the element ID is invalid or not found
     */
    public function duplicateElement(array &$elements, string $elementId): void
    {
        $index = $this->findElementIndex($elements, $elementId);
        if ($index === null) {
            throw new \InvalidArgumentException(__('forms.errors.element_not_found', ['id' => $elementId]));
        }
        $element = $elements[$index];
        $element['id'] = (string) \Illuminate\Support\Str::uuid();
        $element['order'] = count($elements);
        $elements[] = $element;
    }

    /**
     * Find element by ID and return its index.
     *
     * @param array $elements The elements array
     * @param string $elementId The element ID
     * @return int|null The index of the element or null if not found
     */
    public function findElementIndex(array $elements, string $elementId): ?int
    {
        foreach ($elements as $index => $element) {
            if (isset($element['id']) && $element['id'] === $elementId) {
                return $index;
            }
        }

        return null;
    }

    /**
     * Find element by ID.
     *
     * @param array $elements The elements array
     * @param string $elementId The element ID
     * @return array|null The element data or null if not found
     */
    public function findElement(array $elements, string $elementId): ?array
    {
        $index = $this->findElementIndex($elements, $elementId);

        return $index !== null ? $elements[$index] : null;
    }

    /**
     * Get default styles structure.
     */
    private function getDefaultStyles(): array
    {
        return config('forms.elements.default_styles');
    }
}
