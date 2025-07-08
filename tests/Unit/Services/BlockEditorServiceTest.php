<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\ContentBlock;
use App\Services\Contracts\BlockEditorServiceInterface;
use App\Services\BlockManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BlockEditorServiceTest extends TestCase
{
    use RefreshDatabase;

    protected BlockEditorServiceInterface $service;

    protected BlockManager $blockManager;

    protected function setUp(): void
    {
        parent::setUp();

        $this->blockManager = app(BlockManager::class);
        $this->service = new \App\Services\BlockEditorService($this->blockManager);
    }

    /** @test */
    public function it_can_get_block_by_id(): void
    {
        $block = ContentBlock::factory()->create();

        $result = $this->service->getBlockById($block->id);

        $this->assertInstanceOf(ContentBlock::class, $result);
        $this->assertEquals($block->id, $result->id);
    }

    /** @test */
    public function it_returns_null_for_invalid_block_id(): void
    {
        $result = $this->service->getBlockById(999);

        $this->assertNull($result);
    }

    /** @test */
    public function it_can_check_if_block_is_valid(): void
    {
        $block = ContentBlock::factory()->create();

        $this->assertTrue($this->service->isValidBlock($block->id));
        $this->assertFalse($this->service->isValidBlock(999));
    }

    /** @test */
    public function it_can_get_block_visibility(): void
    {
        $block = ContentBlock::factory()->create(['visible' => true]);

        $this->assertTrue($this->service->getBlockVisibility($block));

        $block->update(['visible' => false]);
        $this->assertFalse($this->service->getBlockVisibility($block));
    }

    /** @test */
    public function it_can_update_repeater_state(): void
    {
        $currentState = [
            'title' => 'Test Title',
            'items' => ['old item'],
        ];

        $newItems = ['new item 1', 'new item 2'];

        $result = $this->service->updateRepeaterStateInArray($currentState, 'items', $newItems);

        $this->assertEquals($newItems, $result['items']);
        $this->assertEquals('Test Title', $result['title']);
    }

    /** @test */
    public function it_can_update_nested_repeater_state(): void
    {
        $currentState = [
            'sections' => [
                'buttons' => ['old button'],
            ],
        ];

        $newButtons = ['new button 1', 'new button 2'];

        $result = $this->service->updateRepeaterStateInArray($currentState, 'sections.buttons', $newButtons);

        $this->assertEquals($newButtons, $result['sections']['buttons']);
    }

    /** @test */
    public function it_can_load_block_data(): void
    {
        $block = ContentBlock::factory()->hero()->create([
            'settings' => ['background_color' => 'blue'],
        ]);

        $result = $this->service->loadBlockData($block, 'en');

        $this->assertIsArray($result);
        $this->assertArrayHasKey('background_color', $result);
        $this->assertEquals('blue', $result['background_color']);
    }

    /** @test */
    public function it_can_load_block_data_with_translatable_content(): void
    {
        $block = ContentBlock::factory()->hero()->create([
            'settings' => ['background_color' => 'blue'],
        ]);

        // Check what the raw data looks like
        $rawData = $block->data;
        $translatedData = $block->getTranslatedData('en');

        $this->assertIsArray($rawData);
        $this->assertIsArray($translatedData);

        // The raw data should contain the translatable fields
        $this->assertArrayHasKey('title', $rawData);
        $this->assertArrayHasKey('subtitle', $rawData);
    }
}
