<?php

declare(strict_types=1);

namespace Tests\Unit\Livewire;

use App\Enums\FormStatus;
use App\Enums\MediaType;
use App\Livewire\Admin\Forms\Index as FormsIndex;
use App\Livewire\MediaLibrary;
use App\Models\Form;
use App\Traits\WithEnumHelpers;
use PHPUnit\Framework\TestCase;

class EnumUsageTest extends TestCase
{
    use WithEnumHelpers;

    public function test_forms_index_component_uses_form_status_enum(): void
    {
        $component = new FormsIndex();
        
        // Test that the component can get form status options
        $statusOptions = $component->getAvailableStatuses();
        $this->assertIsArray($statusOptions);
        $this->assertNotEmpty($statusOptions);
        
        // Test that the options match FormStatus enum
        $expectedOptions = FormStatus::options();
        $this->assertEquals($expectedOptions, $statusOptions);
    }

    public function test_media_library_component_uses_media_type_enum(): void
    {
        $component = new MediaLibrary();
        
        // Test that the component can get media type for MIME types
        $imageType = $component->getMediaTypeForMime('image/jpeg');
        $this->assertInstanceOf(MediaType::class, $imageType);
        $this->assertEquals(MediaType::IMAGE, $imageType);
        
        $videoType = $component->getMediaTypeForMime('video/mp4');
        $this->assertInstanceOf(MediaType::class, $videoType);
        $this->assertEquals(MediaType::VIDEO, $videoType);
        
        $documentType = $component->getMediaTypeForMime('application/pdf');
        $this->assertInstanceOf(MediaType::class, $documentType);
        $this->assertEquals(MediaType::DOCUMENT, $documentType);
    }

    public function test_media_library_component_gets_correct_icons(): void
    {
        $component = new MediaLibrary();
        
        $this->assertEquals('photo', $component->getIconForMimeType('image/jpeg'));
        $this->assertEquals('video-camera', $component->getIconForMimeType('video/mp4'));
        $this->assertEquals('musical-note', $component->getIconForMimeType('audio/mpeg'));
        $this->assertEquals('document', $component->getIconForMimeType('application/pdf'));
    }

    public function test_media_library_component_gets_correct_colors(): void
    {
        $component = new MediaLibrary();
        
        $this->assertEquals('blue', $component->getColorForMimeType('image/jpeg'));
        $this->assertEquals('purple', $component->getColorForMimeType('video/mp4'));
        $this->assertEquals('green', $component->getColorForMimeType('audio/mpeg'));
        $this->assertEquals('amber', $component->getColorForMimeType('application/pdf'));
    }

    public function test_media_library_component_gets_correct_labels(): void
    {
        $component = new MediaLibrary();
        
        $this->assertEquals('Image', $component->getLabelForMimeType('image/jpeg'));
        $this->assertEquals('Video', $component->getLabelForMimeType('video/mp4'));
        $this->assertEquals('Audio', $component->getLabelForMimeType('audio/mpeg'));
        $this->assertEquals('Document', $component->getLabelForMimeType('application/pdf'));
    }

    public function test_media_library_component_gets_correct_descriptions(): void
    {
        $component = new MediaLibrary();
        
        $this->assertStringContainsString('Image files', $component->getDescriptionForMimeType('image/jpeg'));
        $this->assertStringContainsString('Video files', $component->getDescriptionForMimeType('video/mp4'));
        $this->assertStringContainsString('Audio files', $component->getDescriptionForMimeType('audio/mpeg'));
        $this->assertStringContainsString('Document files', $component->getDescriptionForMimeType('application/pdf'));
    }

    public function test_with_enum_helpers_trait_provides_badge_data(): void
    {
        // Test form status badge data
        $formStatusBadge = $this->getFormStatusBadge('draft');
        $this->assertArrayHasKey('color', $formStatusBadge);
        $this->assertArrayHasKey('icon', $formStatusBadge);
        $this->assertArrayHasKey('label', $formStatusBadge);
        $this->assertArrayHasKey('description', $formStatusBadge);
        $this->assertEquals('amber', $formStatusBadge['color']);
        $this->assertEquals('document-text', $formStatusBadge['icon']);
        $this->assertEquals('Draft', $formStatusBadge['label']);

        // Test media type badge data
        $mediaTypeBadge = $this->getMediaTypeBadge('image/jpeg');
        $this->assertArrayHasKey('color', $mediaTypeBadge);
        $this->assertArrayHasKey('icon', $mediaTypeBadge);
        $this->assertArrayHasKey('label', $mediaTypeBadge);
        $this->assertArrayHasKey('description', $mediaTypeBadge);
        $this->assertEquals('blue', $mediaTypeBadge['color']);
        $this->assertEquals('photo', $mediaTypeBadge['icon']);
        $this->assertEquals('Image', $mediaTypeBadge['label']);
    }

    public function test_with_enum_helpers_trait_provides_options(): void
    {
        // Test form status options
        $formStatusOptions = $this->getFormStatusOptions();
        $this->assertIsArray($formStatusOptions);
        $this->assertNotEmpty($formStatusOptions);
        $this->assertArrayHasKey('draft', $formStatusOptions);
        $this->assertArrayHasKey('published', $formStatusOptions);
        $this->assertArrayHasKey('archived', $formStatusOptions);

        // Test media type options
        $mediaTypeOptions = $this->getMediaTypeOptions();
        $this->assertIsArray($mediaTypeOptions);
        $this->assertNotEmpty($mediaTypeOptions);
        $this->assertArrayHasKey('image', $mediaTypeOptions);
        $this->assertArrayHasKey('video', $mediaTypeOptions);
        $this->assertArrayHasKey('audio', $mediaTypeOptions);
        $this->assertArrayHasKey('document', $mediaTypeOptions);
    }

    public function test_with_enum_helpers_trait_provides_enum_instances(): void
    {
        // Test form status enum
        $formStatus = $this->getFormStatus('draft');
        $this->assertInstanceOf(FormStatus::class, $formStatus);
        $this->assertEquals(FormStatus::DRAFT, $formStatus);

        // Test media type enum
        $mediaType = $this->getMediaType('image/jpeg');
        $this->assertInstanceOf(MediaType::class, $mediaType);
        $this->assertEquals(MediaType::IMAGE, $mediaType);
    }
} 