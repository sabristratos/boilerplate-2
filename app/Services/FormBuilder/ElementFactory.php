<?php

declare(strict_types=1);

namespace App\Services\FormBuilder;

use App\Services\FormBuilder\Contracts\ElementRendererInterface;
use App\Services\FormBuilder\Renderers\CheckboxRenderer;
use App\Services\FormBuilder\Renderers\DateRenderer;
use App\Services\FormBuilder\Renderers\FileRenderer;
use App\Services\FormBuilder\Renderers\InputRenderer;
use App\Services\FormBuilder\Renderers\NumberRenderer;
use App\Services\FormBuilder\Renderers\PasswordRenderer;
use App\Services\FormBuilder\Renderers\RadioRenderer;
use App\Services\FormBuilder\Renderers\SelectRenderer;
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
        ];
    }

    /**
     * Create a new element with default structure.
     */
    public function createElement(string $type): ElementDTO
    {
        $renderer = $this->getRenderer($type);

        if (! $renderer) {
            throw new \InvalidArgumentException("No renderer found for element type: {$type}");
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
     * Render an element as HTML.
     */
    public function renderElement(array|ElementDTO $element, string $mode = 'edit', ?string $fieldName = null): string
    {
        // Convert array to ElementDTO if needed
        if (is_array($element)) {
            $element = new ElementDTO($element);
        }

        $renderer = $this->getRenderer($element->type);

        if (! $renderer) {
            throw new \InvalidArgumentException("No renderer found for element type: {$element->type}");
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
            } else {
                // Fallback: check common types that each renderer supports
                if ($renderer instanceof InputRenderer) {
                    $types[] = 'text';
                    $types[] = 'email';
                } elseif ($renderer instanceof TextareaRenderer) {
                    $types[] = 'textarea';
                } elseif ($renderer instanceof SelectRenderer) {
                    $types[] = 'select';
                } elseif ($renderer instanceof CheckboxRenderer) {
                    $types[] = 'checkbox';
                } elseif ($renderer instanceof RadioRenderer) {
                    $types[] = 'radio';
                } elseif ($renderer instanceof DateRenderer) {
                    $types[] = 'date';
                } elseif ($renderer instanceof NumberRenderer) {
                    $types[] = 'number';
                } elseif ($renderer instanceof PasswordRenderer) {
                    $types[] = 'password';
                } elseif ($renderer instanceof FileRenderer) {
                    $types[] = 'file';
                }
            }
        }

        return array_unique($types);
    }
}
