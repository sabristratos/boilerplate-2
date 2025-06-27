<?php

namespace App\Services;

use App\Models\Form;
use App\Models\FormField;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class FormService
{
    public function createForm(array $data): Form
    {
        $data['slug'] = Str::slug($data['name']);
        return Form::create($data);
    }

    public function updateForm(Form $form, array $data, string $locale): Form
    {
        if (isset($data['name'])) {
            $data['slug'] = Str::slug($data['name']);
        }
        $this->updateModel($form, $data, $locale);
        return $form;
    }

    public function deleteForm(Form $form): void
    {
        $form->fields()->delete();
        $form->submissions()->delete();
        $form->delete();
    }

    public function createField(Form $form, array $data): FormField
    {
        $data['name'] = $data['name'] ?? 'new_' . $data['type'] . '_' . uniqid();
        $data['sort_order'] = ($form->fields()->max('sort_order') ?? 0) + 1;
        
        return $form->fields()->create($data);
    }

    public function updateField(FormField $field, array $data, string $locale): FormField
    {
        $options = Arr::pull($data, 'options');
        $this->updateModel($field, $data, $locale);

        if (is_array($options)) {
            foreach ($options as $optionData) {
                $option = $field->options()->findOrNew($optionData['id'] ?? null);
                $option->value = $optionData['value'];
                $option->setTranslation('label', $locale, $optionData['label']);
                $option->save();
            }
        }

        return $field;
    }

    public function deleteField(FormField $field): void
    {
        $field->delete();
    }

    private function updateModel(Model $model, array $data, string $locale): void
    {
        foreach ($data as $key => $value) {
            if (in_array($key, $model->getTranslatableAttributes())) {
                $model->setTranslation($key, $locale, $value);
            } else {
                $model->setAttribute($key, $value);
            }
        }
        $model->save();
    }
} 