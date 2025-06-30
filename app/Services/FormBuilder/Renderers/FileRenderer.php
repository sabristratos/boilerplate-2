<?php

namespace App\Services\FormBuilder\Renderers;

class FileRenderer extends BaseElementRenderer
{
    public function render(array $element): string
    {
        $data = $this->prepareViewData($element);

        return view('components.form-builder.elements.file', $data)->render();
    }

    public function supports(string $type): bool
    {
        return $type === 'file';
    }

    protected function getDefaultLabel(): string
    {
        return 'New File Upload';
    }

    protected function getViewName(): string
    {
        return 'components.form-builder.elements.file';
    }

    public function getDefaultProperties(): array
    {
        $properties = parent::getDefaultProperties();

        // Add file-specific properties
        $properties['multiple'] = false;
        $properties['accept'] = '';
        $properties['maxSize'] = '';
        $properties['showPreview'] = true;

        return $properties;
    }
} 