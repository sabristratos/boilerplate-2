<?php

declare(strict_types=1);

namespace App\Services\FormBuilder;

use App\Enums\FormElementType;
use App\Services\FormBuilder\Contracts\ElementRendererInterface;
use App\Services\FormBuilder\Renderers\CheckboxRenderer;
use App\Services\FormBuilder\Renderers\DateRenderer;
use App\Services\FormBuilder\Renderers\FileRenderer;
use App\Services\FormBuilder\Renderers\InputRenderer;
use App\Services\FormBuilder\Renderers\NumberRenderer;
use App\Services\FormBuilder\Renderers\PasswordRenderer;
use App\Services\FormBuilder\Renderers\RadioRenderer;
use App\Services\FormBuilder\Renderers\SelectRenderer;
use App\Services\FormBuilder\Renderers\SubmitButtonRenderer;
use App\Services\FormBuilder\Renderers\TextareaRenderer;
use Illuminate\Support\Str;

/**
 * Factory for creating and rendering form elements.
 */
class ElementFactory
{
    /**
     * @var ElementRendererInterface[]
     */
    private array $renderers;

    /**
     * ElementFactory constructor.
     */
    public function __construct()
    {
        $this->renderers = [
            new InputRenderer,
            new TextareaRenderer,
            new SelectRenderer,
            new CheckboxRenderer,
            new RadioRenderer,
            new DateRenderer,
            new NumberRenderer,
            new PasswordRenderer,
            new FileRenderer,
            new SubmitButtonRenderer,
        ];
    }

    /**
     * Create a new element with default structure.
     *
     * @param string $type The element type
     * @return ElementDTO The created element DTO
     * @throws \InvalidArgumentException If the type is invalid or no renderer is found
     */
    public function createElement(string $type): ElementDTO
    {
        if ($type === '' || $type === '0') {
            throw new \InvalidArgumentException(__('forms.errors.element_type_required'));
        }

        $renderer = $this->getRenderer($type);

        if (!$renderer instanceof \App\Services\FormBuilder\Contracts\ElementRendererInterface) {
            throw new \InvalidArgumentException(__('forms.errors.no_renderer_found', ['type' => $type]));
        }

        return new ElementDTO([
            'id' => (string) Str::uuid(),
            'type' => $type,
            'order' => 0, // Will be set by caller
            'properties' => $renderer->getDefaultProperties(),
            'styles' => $renderer->getDefaultStyles(),
            'validation' => $renderer->getDefaultValidation(),
        ]);
    }

    /**
     * Get the renderer for a specific element type.
     *
     * @param string $type The element type
     * @return ElementRendererInterface|null The renderer instance or null if not found
     */
    public function getRenderer(string $type): ?ElementRendererInterface
    {
        foreach ($this->renderers as $renderer) {
            if ($renderer->supports($type)) {
                return $renderer;
            }
        }

        return null;
    }

    /**
     * Render an element as HTML using the appropriate renderer.
     *
     * @param array|ElementDTO $element The element data or DTO
     * @param string $mode The rendering mode ('edit', 'preview', etc.)
     * @param string|null $fieldName The field name for the element
     * @return string The rendered HTML
     * @throws \InvalidArgumentException If the element type is missing or no renderer is found
     */
    public function renderElement(array|ElementDTO $element, string $mode = 'edit', ?string $fieldName = null): string
    {
        // Convert array to ElementDTO if needed
        if (is_array($element)) {
            if (empty($element['type'])) {
                throw new \InvalidArgumentException('Element type is required');
            }
            $element = new ElementDTO($element);
        }

        if (empty($element->type)) {
            throw new \InvalidArgumentException(__('forms.errors.element_type_required'));
        }

        $renderer = $this->getRenderer($element->type);

        if (!$renderer instanceof \App\Services\FormBuilder\Contracts\ElementRendererInterface) {
            throw new \InvalidArgumentException(__('forms.errors.no_renderer_found', ['type' => $element->type]));
        }

        return $renderer->render($element, $mode, $fieldName);
    }

    /**
     * Get all available renderers.
     *
     * @return ElementRendererInterface[]
     */
    public function getRenderers(): array
    {
        return $this->renderers;
    }

    /**
     * Register a new renderer for a custom element type at runtime.
     *
     * @param ElementRendererInterface $renderer The renderer instance
     */
    public function addRenderer(ElementRendererInterface $renderer): void
    {
        $this->renderers[] = $renderer;
    }

    /**
     * Get supported element types.
     *
     * @return string[]
     */
    public function getSupportedTypes(): array
    {
        $types = [];

        foreach ($this->renderers as $renderer) {
            // Use the renderer's supports method to determine supported types
            if (method_exists($renderer, 'getSupportedTypes')) {
                $types = array_merge($types, $renderer->getSupportedTypes());
            } elseif ($renderer instanceof InputRenderer) {
                // Fallback: check common types that each renderer supports
                $types[] = FormElementType::TEXT->value;
                $types[] = FormElementType::EMAIL->value;
            } elseif ($renderer instanceof TextareaRenderer) {
                $types[] = FormElementType::TEXTAREA->value;
            } elseif ($renderer instanceof SelectRenderer) {
                $types[] = FormElementType::SELECT->value;
            } elseif ($renderer instanceof CheckboxRenderer) {
                $types[] = FormElementType::CHECKBOX->value;
            } elseif ($renderer instanceof RadioRenderer) {
                $types[] = FormElementType::RADIO->value;
            } elseif ($renderer instanceof DateRenderer) {
                $types[] = FormElementType::DATE->value;
            } elseif ($renderer instanceof NumberRenderer) {
                $types[] = FormElementType::NUMBER->value;
            } elseif ($renderer instanceof PasswordRenderer) {
                $types[] = FormElementType::PASSWORD->value;
            } elseif ($renderer instanceof FileRenderer) {
                $types[] = FormElementType::FILE->value;
            } elseif ($renderer instanceof SubmitButtonRenderer) {
                $types[] = FormElementType::SubmitButton->value;
            }
        }

        return array_unique($types);
    }
}
