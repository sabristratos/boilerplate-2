<?php

namespace Tests\Unit;

use App\Services\BlockManager;
use Tests\TestCase;

class BlockManagerTest extends TestCase
{
    /** @test */
    public function it_can_discover_all_blocks()
    {
        $blockManager = app(BlockManager::class);
        $blocks = $blockManager->getAvailableBlocks();

        // Should have at least the original blocks
        $this->assertTrue($blocks->has('content-area'));
        $this->assertTrue($blocks->has('faq-section'));
        $this->assertTrue($blocks->has('hero-section'));

        // Should have our new blocks
        $this->assertTrue($blocks->has('contact'));
        $this->assertTrue($blocks->has('features'));
        $this->assertTrue($blocks->has('testimonials'));
        $this->assertTrue($blocks->has('call-to-action'));
    }

    /** @test */
    public function it_can_find_specific_blocks()
    {
        $blockManager = app(BlockManager::class);

        $contactBlock = $blockManager->find('contact');
        $this->assertNotNull($contactBlock);
        $this->assertEquals('Contact Form', $contactBlock->getName());
        $this->assertEquals('forms', $contactBlock->getCategory());

        $featuresBlock = $blockManager->find('features');
        $this->assertNotNull($featuresBlock);
        $this->assertEquals('Features', $featuresBlock->getName());
        $this->assertEquals('content', $featuresBlock->getCategory());

        $testimonialsBlock = $blockManager->find('testimonials');
        $this->assertNotNull($testimonialsBlock);
        $this->assertEquals('Testimonials', $testimonialsBlock->getName());
        $this->assertEquals('social', $testimonialsBlock->getCategory());

        $ctaBlock = $blockManager->find('call-to-action');
        $this->assertNotNull($ctaBlock);
        $this->assertEquals('Call to Action', $ctaBlock->getName());
        $this->assertEquals('conversion', $ctaBlock->getCategory());
    }

    /** @test */
    public function blocks_have_valid_default_data()
    {
        $blockManager = app(BlockManager::class);

        $contactBlock = $blockManager->find('contact');
        $defaultData = $contactBlock->getDefaultData();
        $this->assertArrayHasKey('heading', $defaultData);
        $this->assertArrayHasKey('form_id', $defaultData);
        $this->assertArrayHasKey('background_color', $defaultData);

        $featuresBlock = $blockManager->find('features');
        $defaultData = $featuresBlock->getDefaultData();
        $this->assertArrayHasKey('heading', $defaultData);
        $this->assertArrayHasKey('features', $defaultData);
        $this->assertIsArray($defaultData['features']);

        $testimonialsBlock = $blockManager->find('testimonials');
        $defaultData = $testimonialsBlock->getDefaultData();
        $this->assertArrayHasKey('heading', $defaultData);
        $this->assertArrayHasKey('testimonials', $defaultData);
        $this->assertIsArray($defaultData['testimonials']);

        $ctaBlock = $blockManager->find('call-to-action');
        $defaultData = $ctaBlock->getDefaultData();
        $this->assertArrayHasKey('heading', $defaultData);
        $this->assertArrayHasKey('buttons', $defaultData);
        $this->assertIsArray($defaultData['buttons']);
    }
} 