<?php

declare(strict_types=1);

use App\Services\FormBuilder\ElementDTO;
use App\Services\FormBuilder\ElementFactory;

describe('ElementFactory', function (): void {
    beforeEach(function (): void {
        $this->factory = new ElementFactory;
    });

    describe('createElement', function (): void {
        it('can create a text element', function (): void {
            $element = $this->factory->createElement('text', 'field_1');

            expect($element)->toBeInstanceOf(ElementDTO::class)
                ->and($element->type)->toBe('text')
                ->and($element->id)->toBeString()
                ->and($element->order)->toBe(0)
                ->and($element->properties)->toBeArray()
                ->and($element->properties['label'])->toBe('New Input')
                ->and($element->properties['placeholder'])->toBe('')
                ->and($element->properties['fluxProps'])->toBeArray()
                ->and($element->validation)->toBeArray()
                ->and($element->validation['rules'])->toBeArray();
        });

        it('can create an email element', function (): void {
            $element = $this->factory->createElement('email', 'field_2');

            expect($element)->toBeInstanceOf(ElementDTO::class)
                ->and($element->type)->toBe('email')
                ->and($element->id)->toBeString()
                ->and($element->properties['label'])->toBe('New Input')
                ->and($element->validation['rules'])->toBeArray();
        });

        it('can create a textarea element', function (): void {
            $element = $this->factory->createElement('textarea', 'field_3');

            expect($element)->toBeInstanceOf(ElementDTO::class)
                ->and($element->type)->toBe('textarea')
                ->and($element->id)->toBeString()
                ->and($element->properties['label'])->toBe('New Textarea')
                ->and($element->properties['placeholder'])->toBe('');
        });

        it('can create a select element', function (): void {
            $element = $this->factory->createElement('select', 'field_4');

            expect($element)->toBeInstanceOf(ElementDTO::class)
                ->and($element->type)->toBe('select')
                ->and($element->id)->toBeString()
                ->and($element->properties['label'])->toBe('New Select')
                ->and($element->properties['placeholder'])->toBe('');
        });

        it('can create a checkbox element', function (): void {
            $element = $this->factory->createElement('checkbox', 'field_5');

            expect($element)->toBeInstanceOf(ElementDTO::class)
                ->and($element->type)->toBe('checkbox')
                ->and($element->id)->toBeString()
                ->and($element->properties['label'])->toBe('New Checkbox')
                ->and($element->properties['placeholder'])->toBe('');
        });

        it('can create a radio element', function (): void {
            $element = $this->factory->createElement('radio', 'field_6');

            expect($element)->toBeInstanceOf(ElementDTO::class)
                ->and($element->type)->toBe('radio')
                ->and($element->id)->toBeString()
                ->and($element->properties['label'])->toBe('New Radio')
                ->and($element->properties['placeholder'])->toBe('');
        });

        it('can create a date element', function (): void {
            $element = $this->factory->createElement('date', 'field_7');

            expect($element)->toBeInstanceOf(ElementDTO::class)
                ->and($element->type)->toBe('date')
                ->and($element->id)->toBeString()
                ->and($element->properties['label'])->toBe('New Date');
        });

        it('can create a file element', function (): void {
            $element = $this->factory->createElement('file', 'field_8');

            expect($element)->toBeInstanceOf(ElementDTO::class)
                ->and($element->type)->toBe('file')
                ->and($element->id)->toBeString()
                ->and($element->properties['label'])->toBe('New File')
                ->and($element->properties['placeholder'])->toBe('');
        });

        it('can create a number element', function (): void {
            $element = $this->factory->createElement('number', 'field_9');

            expect($element)->toBeInstanceOf(ElementDTO::class)
                ->and($element->type)->toBe('number')
                ->and($element->id)->toBeString()
                ->and($element->properties['label'])->toBe('New Number')
                ->and($element->properties['placeholder'])->toBe('');
        });

        it('can create a password element', function (): void {
            $element = $this->factory->createElement('password', 'field_12');

            expect($element)->toBeInstanceOf(ElementDTO::class)
                ->and($element->type)->toBe('password')
                ->and($element->id)->toBeString()
                ->and($element->properties['label'])->toBe('New Password');
        });

        it('throws exception for unknown element type', function (): void {
            expect(fn () => $this->factory->createElement('unknown', 'field_19'))
                ->toThrow(InvalidArgumentException::class, 'No renderer found for element type: unknown');
        });
    });

    describe('renderElement', function (): void {
        it('can render a text element', function (): void {
            $element = [
                'id' => 'test_1',
                'type' => \App\Enums\FormElementType::TEXT->value,
                'properties' => ['label' => 'Test Field'],
            ];

            $rendered = $this->factory->renderElement($element);

            expect($rendered)->toBeString()
                ->and($rendered)->toContain('Test Field');
        });

        it('throws exception for unknown element type', function (): void {
            $element = [
                'id' => 'test_1',
                'type' => 'unknown',
                'properties' => ['label' => 'Test Field'],
            ];

            expect(fn () => $this->factory->renderElement($element))
                ->toThrow(InvalidArgumentException::class, 'No renderer found for element type: unknown');
        });
    });
});
