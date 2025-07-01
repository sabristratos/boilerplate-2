<?php

namespace App\Services\FormBuilder\Renderers;

class CheckboxRenderer extends BaseElementRenderer
{
    public function render(array $element): string
    {
        $data = $this->prepareViewData($element);

        return view('components.form-builder.elements.checkbox', $data)->render();
    }

    public function supports(string $type): bool
    {
        return $type === 'checkbox';
    }

    protected function getDefaultLabel(): string
    {
        return 'New Checkbox';
    }

    protected function getViewName(): string
    {
        return 'components.form-builder.elements.checkbox';
    }

    /**
     * Get supported element types for this renderer
     */
    public function getSupportedTypes(): array
    {
        return ['checkbox'];
    }
}
