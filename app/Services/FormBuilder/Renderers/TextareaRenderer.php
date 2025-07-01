<?php

namespace App\Services\FormBuilder\Renderers;

class TextareaRenderer extends BaseElementRenderer
{
    public function render(array $element): string
    {
        $data = $this->prepareViewData($element);

        return view('components.form-builder.elements.textarea', $data)->render();
    }

    public function supports(string $type): bool
    {
        return $type === 'textarea';
    }

    protected function getDefaultLabel(): string
    {
        return 'New Textarea';
    }

    protected function getViewName(): string
    {
        return 'components.form-builder.elements.textarea';
    }

    /**
     * Get supported element types for this renderer
     */
    public function getSupportedTypes(): array
    {
        return ['textarea'];
    }
}
