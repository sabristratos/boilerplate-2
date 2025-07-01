<?php

namespace Tests\Unit;

use App\Services\FormBuilder\FieldNameGeneratorService;
use Tests\TestCase;

class FieldNameGeneratorServiceTest extends TestCase
{
    private FieldNameGeneratorService $fieldNameGenerator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->fieldNameGenerator = new FieldNameGeneratorService();
    }

    /** @test */
    public function it_generates_readable_field_names_from_labels()
    {
        $element = [
            'id' => '123',
            'properties' => [
                'label' => 'Full Name'
            ]
        ];

        $fieldName = $this->fieldNameGenerator->generateFieldName($element);

        $this->assertEquals('full_name', $fieldName);
    }

    /** @test */
    public function it_generates_field_names_with_special_characters()
    {
        $element = [
            'id' => '456',
            'properties' => [
                'label' => 'Email Address (Primary)'
            ]
        ];

        $fieldName = $this->fieldNameGenerator->generateFieldName($element);

        $this->assertEquals('email_address_primary', $fieldName);
    }

    /** @test */
    public function it_falls_back_to_id_when_label_is_empty()
    {
        $element = [
            'id' => '789',
            'properties' => [
                'label' => ''
            ]
        ];

        $fieldName = $this->fieldNameGenerator->generateFieldName($element);

        $this->assertEquals('field_789', $fieldName);
    }

    /** @test */
    public function it_falls_back_to_id_when_label_is_missing()
    {
        $element = [
            'id' => '101',
            'properties' => []
        ];

        $fieldName = $this->fieldNameGenerator->generateFieldName($element);

        $this->assertEquals('field_101', $fieldName);
    }

    /** @test */
    public function it_generates_consistent_names_for_same_labels()
    {
        $element1 = [
            'id' => '123',
            'properties' => [
                'label' => 'Full Name'
            ]
        ];

        $element2 = [
            'id' => '456',
            'properties' => [
                'label' => 'Full Name'
            ]
        ];

        $fieldName1 = $this->fieldNameGenerator->generateFieldName($element1);
        $fieldName2 = $this->fieldNameGenerator->generateFieldName($element2);

        $this->assertEquals($fieldName1, $fieldName2);
        $this->assertEquals('full_name', $fieldName1);
    }

    /** @test */
    public function it_handles_simple_field_name_method_for_backward_compatibility()
    {
        $element = [
            'id' => '123',
            'properties' => [
                'label' => 'Full Name'
            ]
        ];

        $simpleFieldName = $this->fieldNameGenerator->generateSimpleFieldName($element);
        $fieldName = $this->fieldNameGenerator->generateFieldName($element);

        // Both methods should now return the same result
        $this->assertEquals($fieldName, $simpleFieldName);
        $this->assertEquals('full_name', $simpleFieldName);
    }
} 