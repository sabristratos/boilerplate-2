<?php

namespace App\Services\FormBuilder;

<<<<<<< HEAD
use App\Services\FormBuilder\ElementFactory;

=======
/**
 * Manages operations on form elements (add, update, delete, reorder, etc.).
 */
>>>>>>> 3d646ebc8597a7b3e698f9f41fc701b941fde20d
class ElementManager
{
    private ElementFactory $factory;

    /**
     * ElementManager constructor.
     *
     * @param ElementFactory $factory
     */
    public function __construct(ElementFactory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * Add a new element to the elements array.
     *
     * @param array $elements
     * @param string $type
     * @return void
     */
    public function addElement(array &$elements, string $type): void
    {
        $newElement = $this->factory->createElement($type);
        $newElement->order = count($elements);
        $elements[] = $newElement->toArray();
    }

    /**
     * Update an existing element.
     *
     * @param array $elements
     * @param string $elementId
     * @param array $updates
     * @return void
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
     *
     * @param array $elements
     * @param string $elementId
     * @return void
     */
    public function deleteElement(array &$elements, string $elementId): void
    {
        $elements = array_filter($elements, fn ($element) => $element['id'] !== $elementId);
        $elements = array_values($elements); // Re-index array
    }

    /**
     * Reorder elements based on new order.
     *
     * @param array $elements
     * @param array $orderedOrders
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
     * @param array $elements
     * @param string $elementId
     * @param string $breakpoint
     * @param string $width
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
     * Find element by ID and return its index.
     *
     * @param array $elements
     * @param string $elementId
     * @return int|null
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
     * @param array $elements
     * @param string $elementId
     * @return array|null
     */
    public function findElement(array $elements, string $elementId): ?array
    {
        $index = $this->findElementIndex($elements, $elementId);

        return $index !== null ? $elements[$index] : null;
    }

    /**
     * Get default styles structure.
     *
     * @return array
     */
    private function getDefaultStyles(): array
    {
        return config('forms.elements.default_styles');
    }
}
