<?php

namespace Tests\Unit;

use App\Services\FormBuilder\ValidationRuleService;
use App\Enums\FormElementType;
use Tests\TestCase;

class ValidationRuleServiceTest extends TestCase
{
    private ValidationRuleService $validationRuleService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->validationRuleService = new ValidationRuleService();
    }

    /** @test */
    public function it_returns_all_validation_rules_from_config()
    {
        $rules = $this->validationRuleService->getAllRules();
        
        $this->assertIsArray($rules);
        $this->assertNotEmpty($rules);
        $this->assertArrayHasKey('required', $rules);
        $this->assertArrayHasKey('email', $rules);
    }

    /** @test */
    public function it_returns_relevant_rules_for_text_field()
    {
        $rules = $this->validationRuleService->getRelevantRules(FormElementType::TEXT->value);
        
        $this->assertIsArray($rules);
        $this->assertArrayHasKey('required', $rules);
        $this->assertArrayHasKey('min', $rules);
        $this->assertArrayHasKey('max', $rules);
        $this->assertArrayHasKey('alpha', $rules);
        $this->assertArrayNotHasKey('numeric', $rules); // Should not be available for text
    }

    /** @test */
    public function it_returns_relevant_rules_for_email_field()
    {
        $rules = $this->validationRuleService->getRelevantRules(FormElementType::EMAIL->value);
        
        $this->assertIsArray($rules);
        $this->assertArrayHasKey('required', $rules);
        $this->assertArrayHasKey('email', $rules);
        $this->assertArrayNotHasKey('alpha', $rules); // Should not be available for email
    }

    /** @test */
    public function it_returns_relevant_rules_for_number_field()
    {
        $rules = $this->validationRuleService->getRelevantRules(FormElementType::NUMBER->value);
        
        $this->assertIsArray($rules);
        $this->assertArrayHasKey('required', $rules);
        $this->assertArrayHasKey('numeric', $rules);
        $this->assertArrayHasKey('min_value', $rules);
        $this->assertArrayHasKey('max_value', $rules);
    }

    /** @test */
    public function it_groups_rules_by_category()
    {
        $groupedRules = $this->validationRuleService->getRelevantRulesByCategory(FormElementType::TEXT->value);
        
        $this->assertIsArray($groupedRules);
        $this->assertArrayHasKey('Basic', $groupedRules);
        $this->assertArrayHasKey('Length', $groupedRules);
        $this->assertArrayHasKey('Format', $groupedRules);
    }

    /** @test */
    public function it_generates_validation_rules_for_element()
    {
        $element = [
            'validation' => [
                'rules' => ['required', 'min'],
                'values' => ['min' => '5']
            ]
        ];

        $rules = $this->validationRuleService->generateRules($element);
        
        $this->assertIsArray($rules);
        $this->assertContains('required', $rules);
        $this->assertContains('min:5', $rules);
    }

    /** @test */
    public function it_generates_validation_messages_for_element()
    {
        $element = [
            'properties' => ['label' => 'Test Field'],
            'validation' => [
                'rules' => ['required'],
                'messages' => ['required' => 'Custom message']
            ]
        ];

        $messages = $this->validationRuleService->generateMessages($element);
        
        $this->assertIsArray($messages);
        $this->assertArrayHasKey('required', $messages);
        $this->assertEquals('Custom message', $messages['required']);
    }

    /** @test */
    public function it_returns_available_categories_for_field_type()
    {
        $categories = $this->validationRuleService->getAvailableCategories(FormElementType::TEXT->value);
        
        $this->assertIsArray($categories);
        $this->assertContains('Basic', $categories);
        $this->assertContains('Length', $categories);
        $this->assertContains('Format', $categories);
    }
} 