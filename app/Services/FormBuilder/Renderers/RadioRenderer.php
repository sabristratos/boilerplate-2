<?php

namespace App\Services\FormBuilder\Renderers;

class RadioRenderer extends BaseElementRenderer
{
    public function render(array $element): string
    {
        $data = $this->prepareViewData($element);

        return view('components.form-builder.elements.radio', $data)->render();
    }

    public function supports(string $type): bool
    {
        return $type === 'radio';
    }

    protected function getDefaultLabel(): string
    {
        return 'New Radio';
    }

    protected function getViewName(): string
    {
        return 'components.form-builder.elements.radio';
    }
}
