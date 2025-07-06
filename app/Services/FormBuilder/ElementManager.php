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
     */
    public function addElement(array &$elements, string $type): void
    {
        $newElement = $this->factory->createElement($type);
        $newElement->order = count($elements);
        $elements[] = $newElement->toArray();
    }

    /**
     * Update an existing element.
     */
    public function updateElement(array &$elements, string $elementId, array $updates): void
    {
        $index = $this->findElementIndex($elements, $elementId);
        if ($index !== null) {
            $elements[$index] = array_merge($elements[$index], $updates);
        }
    }

    /**
     * Delete an element from the elements array.
     */
    public function deleteElement(array &$elements, string $elementId): void
    {
        $elements = array_filter($elements, fn ($element) => $element['id'] !== $elementId);
        $elements = array_values($elements); // Re-index array
    }

    /**
     * Reorder elements based on new order.
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
     * Find element by ID and return its index.
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
