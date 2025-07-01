<?php

use App\Enums\FormElementType;
use App\Services\FormBuilder\FieldValidationService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('FieldValidationService', function () {
    beforeEach(function () {
        $this->fieldValidationService = new FieldValidationService();
    });

    describe('Field-specific validation rules', function () {
        it('returns correct validation rules for text fields', function () {
            $rules = $this->fieldValidationService->getRelevantRules('text');
            
            expect($rules)->toHaveKey('required');
            expect($rules)->toHaveKey('min');
            expect($rules)->toHaveKey('max');
            expect($rules)->toHaveKey('alpha');
            expect($rules)->toHaveKey('alpha_num');
            expect($rules)->toHaveKey('url');
            expect($rules)->toHaveKey('regex');
        });

        it('returns correct validation rules for email fields', function () {
            $rules = $this->fieldValidationService->getRelevantRules('email');
            
            expect($rules)->toHaveKey('required');
            expect($rules)->toHaveKey('email');
            expect($rules)->toHaveKey('max');
        });

        it('returns correct validation rules for number fields', function () {
            $rules = $this->fieldValidationService->getRelevantRules('number');
            
            expect($rules)->toHaveKey('required');
            expect($rules)->toHaveKey('numeric');
            expect($rules)->toHaveKey('integer');
            expect($rules)->toHaveKey('min_value');
            expect($rules)->toHaveKey('max_value');
        });

        it('returns correct validation rules for date fields', function () {
            $rules = $this->fieldValidationService->getRelevantRules('date');
            
            expect($rules)->toHaveKey('required');
            expect($rules)->toHaveKey('date');
            expect($rules)->toHaveKey('date_after');
            expect($rules)->toHaveKey('date_before');
        });

        it('returns correct validation rules for file fields', function () {
            $rules = $this->fieldValidationService->getRelevantRules('file');
            
            expect($rules)->toHaveKey('required');
            expect($rules)->toHaveKey('file');
            expect($rules)->toHaveKey('image');
            expect($rules)->toHaveKey('mimes');
            expect($rules)->toHaveKey('max_file_size');
        });

        it('returns correct validation rules for password fields', function () {
            $rules = $this->fieldValidationService->getRelevantRules('password');
            
            expect($rules)->toHaveKey('required');
            expect($rules)->toHaveKey('min');
            expect($rules)->toHaveKey('max');
            expect($rules)->toHaveKey('confirmed');
        });

        it('returns correct validation rules for select fields', function () {
            $rules = $this->fieldValidationService->getRelevantRules('select');
            
            expect($rules)->toHaveKey('required');
        });

        it('returns correct validation rules for checkbox fields', function () {
            $rules = $this->fieldValidationService->getRelevantRules('checkbox');
            
            expect($rules)->toHaveKey('required');
        });

        it('returns correct validation rules for radio fields', function () {
            $rules = $this->fieldValidationService->getRelevantRules('radio');
            
            expect($rules)->toHaveKey('required');
        });

        it('returns correct validation rules for textarea fields', function () {
            $rules = $this->fieldValidationService->getRelevantRules('textarea');
            
            expect($rules)->toHaveKey('required');
            expect($rules)->toHaveKey('min');
            expect($rules)->toHaveKey('max');
        });
    });

    describe('Validation rule categorization', function () {
        it('groups validation rules by category correctly', function () {
            $categories = $this->fieldValidationService->getRelevantRulesByCategory('text');
            
            expect($categories)->toHaveKey('Basic');
            expect($categories)->toHaveKey('Length');
            expect($categories)->toHaveKey('Format');
            expect($categories)->toHaveKey('Advanced');
        });

        it('returns correct categories for text fields', function () {
            $categories = $this->fieldValidationService->getAvailableCategories('text');
            
            expect($categories)->toContain('Basic');
            expect($categories)->toContain('Length');
            expect($categories)->toContain('Format');
            expect($categories)->toContain('Advanced');
        });

        it('returns correct categories for number fields', function () {
            $categories = $this->fieldValidationService->getAvailableCategories('number');
            
            expect($categories)->toContain('Basic');
            expect($categories)->toContain('Range');
            expect($categories)->toContain('Format');
        });

        it('returns correct categories for date fields', function () {
            $categories = $this->fieldValidationService->getAvailableCategories('date');
            
            expect($categories)->toContain('Basic');
            expect($categories)->toContain('Range');
            expect($categories)->toContain('Format');
        });

        it('returns correct categories for file fields', function () {
            $categories = $this->fieldValidationService->getAvailableCategories('file');
            
            expect($categories)->toContain('Basic');
            expect($categories)->toContain('Format');
            expect($categories)->toContain('Size');
        });
    });

    describe('Validation rule structure', function () {
        it('has correct structure for text rules', function () {
            $rules = $this->fieldValidationService->getRelevantRules('text');
            
            foreach ($rules as $ruleKey => $rule) {
                expect($rule)->toHaveKey('label');
                expect($rule)->toHaveKey('description');
                expect($rule)->toHaveKey('rule');
                expect($rule)->toHaveKey('icon');
                expect($rule)->toHaveKey('has_value');
                expect($rule)->toHaveKey('category');
            }
        });

        it('has correct structure for email rules', function () {
            $rules = $this->fieldValidationService->getRelevantRules('email');
            
            foreach ($rules as $ruleKey => $rule) {
                expect($rule)->toHaveKey('label');
                expect($rule)->toHaveKey('description');
                expect($rule)->toHaveKey('rule');
                expect($rule)->toHaveKey('icon');
                expect($rule)->toHaveKey('has_value');
                expect($rule)->toHaveKey('category');
            }
        });

        it('has correct structure for number rules', function () {
            $rules = $this->fieldValidationService->getRelevantRules('number');
            
            foreach ($rules as $ruleKey => $rule) {
                expect($rule)->toHaveKey('label');
                expect($rule)->toHaveKey('description');
                expect($rule)->toHaveKey('rule');
                expect($rule)->toHaveKey('icon');
                expect($rule)->toHaveKey('has_value');
                expect($rule)->toHaveKey('category');
            }
        });
    });

    describe('Edge cases and error handling', function () {
        it('handles invalid field types gracefully', function () {
            $rules = $this->fieldValidationService->getRelevantRules('invalid_type');
            
            // Should return default rules (just 'required' rule)
            expect($rules)->toHaveKey('required');
            expect($rules)->toHaveCount(1);
        });

        it('returns basic category for invalid field type categories', function () {
            $categories = $this->fieldValidationService->getAvailableCategories('invalid_type');
            
            expect($categories)->toContain('Basic');
            expect($categories)->toHaveCount(1);
        });

        it('returns basic category rules for invalid field type categorized rules', function () {
            $categorizedRules = $this->fieldValidationService->getRelevantRulesByCategory('invalid_type');
            
            expect($categorizedRules)->toHaveKey('Basic');
            expect($categorizedRules['Basic'])->toHaveKey('required');
        });
    });

    describe('Rule validation', function () {
        it('validates required rule structure', function () {
            $rules = $this->fieldValidationService->getRelevantRules('text');
            
            expect($rules['required']['rule'])->toBe('required');
            expect($rules['required']['has_value'])->toBeFalse();
            expect($rules['required']['category'])->toBe('Basic');
        });

        it('validates min rule structure', function () {
            $rules = $this->fieldValidationService->getRelevantRules('text');
            
            expect($rules['min']['rule'])->toBe('min');
            expect($rules['min']['has_value'])->toBeTrue();
            expect($rules['min']['category'])->toBe('Length');
        });

        it('validates max rule structure', function () {
            $rules = $this->fieldValidationService->getRelevantRules('text');
            
            expect($rules['max']['rule'])->toBe('max');
            expect($rules['max']['has_value'])->toBeTrue();
            expect($rules['max']['category'])->toBe('Length');
        });

        it('validates email rule structure', function () {
            $rules = $this->fieldValidationService->getRelevantRules('email');
            
            expect($rules['email']['rule'])->toBe('email');
            expect($rules['email']['has_value'])->toBeFalse();
            expect($rules['email']['category'])->toBe('Format');
        });

        it('validates numeric rule structure', function () {
            $rules = $this->fieldValidationService->getRelevantRules('number');
            
            expect($rules['numeric']['rule'])->toBe('numeric');
            expect($rules['numeric']['has_value'])->toBeFalse();
            expect($rules['numeric']['category'])->toBe('Format');
        });

        it('validates date rule structure', function () {
            $rules = $this->fieldValidationService->getRelevantRules('date');
            
            expect($rules['date']['rule'])->toBe('date');
            expect($rules['date']['has_value'])->toBeFalse();
            expect($rules['date']['category'])->toBe('Format');
        });

        it('validates url rule structure', function () {
            $rules = $this->fieldValidationService->getRelevantRules('text');
            
            expect($rules['url']['rule'])->toBe('url');
            expect($rules['url']['has_value'])->toBeFalse();
            expect($rules['url']['category'])->toBe('Format');
        });

        it('validates alpha rule structure', function () {
            $rules = $this->fieldValidationService->getRelevantRules('text');
            
            expect($rules['alpha']['rule'])->toBe('alpha');
            expect($rules['alpha']['has_value'])->toBeFalse();
            expect($rules['alpha']['category'])->toBe('Format');
        });

        it('validates alpha_num rule structure', function () {
            $rules = $this->fieldValidationService->getRelevantRules('text');
            
            expect($rules['alpha_num']['rule'])->toBe('alpha_num');
            expect($rules['alpha_num']['has_value'])->toBeFalse();
            expect($rules['alpha_num']['category'])->toBe('Format');
        });
    });

    describe('Complex validation scenarios', function () {
        it('handles multiple field types correctly', function () {
            $textRules = $this->fieldValidationService->getRelevantRules('text');
            $emailRules = $this->fieldValidationService->getRelevantRules('email');
            $numberRules = $this->fieldValidationService->getRelevantRules('number');
            
            expect($textRules)->not->toBe($emailRules);
            expect($emailRules)->not->toBe($numberRules);
            expect($numberRules)->not->toBe($textRules);
        });

        it('handles categorization for multiple field types', function () {
            $textCategories = $this->fieldValidationService->getAvailableCategories('text');
            $emailCategories = $this->fieldValidationService->getAvailableCategories('email');
            $numberCategories = $this->fieldValidationService->getAvailableCategories('number');
            
            expect($textCategories)->toContain('Basic');
            expect($emailCategories)->toContain('Basic');
            expect($numberCategories)->toContain('Basic');
            
            expect($textCategories)->toContain('Format');
            expect($emailCategories)->toContain('Format');
            expect($numberCategories)->toContain('Format');
        });

        it('handles rule grouping for multiple field types', function () {
            $textCategorized = $this->fieldValidationService->getRelevantRulesByCategory('text');
            $emailCategorized = $this->fieldValidationService->getRelevantRulesByCategory('email');
            $numberCategorized = $this->fieldValidationService->getRelevantRulesByCategory('number');
            
            expect($textCategorized)->toHaveKey('Basic');
            expect($emailCategorized)->toHaveKey('Basic');
            expect($numberCategorized)->toHaveKey('Basic');
            
            expect($textCategorized)->toHaveKey('Format');
            expect($emailCategorized)->toHaveKey('Format');
            expect($numberCategorized)->toHaveKey('Format');
        });
    });
}); 