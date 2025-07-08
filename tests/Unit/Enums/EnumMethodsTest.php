<?php

declare(strict_types=1);

namespace Tests\Unit\Enums;

use App\Enums\ContentBlockStatus;
use App\Enums\FormElementType;
use App\Enums\FormStatus;
use App\Enums\MediaType;
use App\Enums\NotificationType;
use App\Enums\PublishStatus;
use App\Enums\SettingGroupKey;
use App\Enums\SettingType;
use App\Enums\UserRole;
use PHPUnit\Framework\TestCase;

class EnumMethodsTest extends TestCase
{
    public function test_content_block_status_has_all_required_methods(): void
    {
        $this->assertNotEmpty(ContentBlockStatus::DRAFT->getColor());
        $this->assertNotEmpty(ContentBlockStatus::DRAFT->getIcon());
        $this->assertNotEmpty(ContentBlockStatus::DRAFT->getDescription());
        $this->assertNotEmpty(ContentBlockStatus::DRAFT->label());
        $this->assertTrue(ContentBlockStatus::DRAFT->isDraft());
        $this->assertFalse(ContentBlockStatus::DRAFT->isPublished());
        $this->assertTrue(ContentBlockStatus::PUBLISHED->isPublished());
        $this->assertFalse(ContentBlockStatus::PUBLISHED->isDraft());
    }

    public function test_form_status_has_all_required_methods(): void
    {
        $this->assertNotEmpty(FormStatus::DRAFT->getColor());
        $this->assertNotEmpty(FormStatus::DRAFT->getIcon());
        $this->assertNotEmpty(FormStatus::DRAFT->getDescription());
        $this->assertNotEmpty(FormStatus::DRAFT->label());
        $this->assertTrue(FormStatus::DRAFT->isDraft());
        $this->assertTrue(FormStatus::PUBLISHED->isPublished());
        $this->assertTrue(FormStatus::ARCHIVED->isArchived());
    }

    public function test_form_element_type_has_all_required_methods(): void
    {
        $this->assertNotEmpty(FormElementType::TEXT->getColor());
        $this->assertNotEmpty(FormElementType::TEXT->getIcon());
        $this->assertNotEmpty(FormElementType::TEXT->getDescription());
        $this->assertNotEmpty(FormElementType::TEXT->label());
        $this->assertTrue(FormElementType::TEXT->isTextInput());
        $this->assertTrue(FormElementType::SELECT->isChoiceInput());
        $this->assertTrue(FormElementType::DATE->isDateTimeInput());
        $this->assertTrue(FormElementType::NUMBER->isNumericInput());
        $this->assertTrue(FormElementType::FILE->isFileInput());
        $this->assertTrue(FormElementType::SubmitButton->isSubmitButton());
    }

    public function test_media_type_has_all_required_methods(): void
    {
        $this->assertNotEmpty(MediaType::IMAGE->getColor());
        $this->assertNotEmpty(MediaType::IMAGE->getIcon());
        $this->assertNotEmpty(MediaType::IMAGE->getDescription());
        $this->assertNotEmpty(MediaType::IMAGE->label());
        $this->assertTrue(MediaType::IMAGE->isImage());
        $this->assertTrue(MediaType::VIDEO->isVideo());
        $this->assertTrue(MediaType::AUDIO->isAudio());
        $this->assertTrue(MediaType::DOCUMENT->isDocument());
        $this->assertTrue(MediaType::ARCHIVE->isArchive());
    }

    public function test_notification_type_has_all_required_methods(): void
    {
        $this->assertNotEmpty(NotificationType::SUCCESS->getColor());
        $this->assertNotEmpty(NotificationType::SUCCESS->getIcon());
        $this->assertNotEmpty(NotificationType::SUCCESS->getDescription());
        $this->assertNotEmpty(NotificationType::SUCCESS->label());
        $this->assertTrue(NotificationType::SUCCESS->isSuccess());
        $this->assertTrue(NotificationType::WARNING->isWarning());
        $this->assertTrue(NotificationType::ERROR->isError());
        $this->assertTrue(NotificationType::INFO->isInfo());
    }

    public function test_publish_status_has_all_required_methods(): void
    {
        $this->assertNotEmpty(PublishStatus::DRAFT->getColor());
        $this->assertNotEmpty(PublishStatus::DRAFT->getIcon());
        $this->assertNotEmpty(PublishStatus::DRAFT->getDescription());
        $this->assertNotEmpty(PublishStatus::DRAFT->label());
        $this->assertTrue(PublishStatus::DRAFT->isDraft());
        $this->assertTrue(PublishStatus::PUBLISHED->isPublished());
    }

    public function test_setting_group_key_has_all_required_methods(): void
    {
        $this->assertNotEmpty(SettingGroupKey::GENERAL->getColor());
        $this->assertNotEmpty(SettingGroupKey::GENERAL->getIcon());
        $this->assertNotEmpty(SettingGroupKey::GENERAL->getDescription());
        $this->assertNotEmpty(SettingGroupKey::GENERAL->label());
        $this->assertEquals(1, SettingGroupKey::GENERAL->getOrder());
        $this->assertEquals(10, SettingGroupKey::ADVANCED->getOrder());
    }

    public function test_setting_type_has_all_required_methods(): void
    {
        $this->assertNotEmpty(SettingType::TEXT->getColor());
        $this->assertNotEmpty(SettingType::TEXT->getIcon());
        $this->assertNotEmpty(SettingType::TEXT->getDescription());
        $this->assertNotEmpty(SettingType::TEXT->label());
        $this->assertTrue(SettingType::TEXT->isTextInput());
        $this->assertTrue(SettingType::NUMBER->isNumericInput());
        $this->assertTrue(SettingType::DATE->isDateTimeInput());
        $this->assertTrue(SettingType::FILE->isFileInput());
    }

    public function test_user_role_has_all_required_methods(): void
    {
        $this->assertNotEmpty(UserRole::ADMIN->getColor());
        $this->assertNotEmpty(UserRole::ADMIN->getIcon());
        $this->assertNotEmpty(UserRole::ADMIN->getDescription());
        $this->assertNotEmpty(UserRole::ADMIN->label());
        $this->assertTrue(UserRole::SUPER_ADMIN->isSuperAdmin());
        $this->assertTrue(UserRole::ADMIN->isAdmin());
        $this->assertTrue(UserRole::EDITOR->isEditor());
        $this->assertTrue(UserRole::USER->isUser());
        $this->assertEquals(4, UserRole::SUPER_ADMIN->getPermissionLevel());
        $this->assertEquals(1, UserRole::USER->getPermissionLevel());
    }

    public function test_all_enums_have_options_method(): void
    {
        $this->assertNotEmpty(ContentBlockStatus::options());
        $this->assertNotEmpty(FormStatus::options());
        $this->assertNotEmpty(FormElementType::options());
        $this->assertNotEmpty(MediaType::options());
        $this->assertNotEmpty(NotificationType::options());
        $this->assertNotEmpty(PublishStatus::options());
        $this->assertNotEmpty(SettingGroupKey::options());
        $this->assertNotEmpty(SettingType::options());
        $this->assertNotEmpty(UserRole::options());
    }

    public function test_all_enums_have_values_method(): void
    {
        $this->assertNotEmpty(ContentBlockStatus::values());
        $this->assertNotEmpty(FormStatus::values());
        $this->assertNotEmpty(FormElementType::values());
        $this->assertNotEmpty(MediaType::values());
        $this->assertNotEmpty(NotificationType::values());
        $this->assertNotEmpty(PublishStatus::values());
        $this->assertNotEmpty(SettingGroupKey::values());
        $this->assertNotEmpty(SettingType::values());
        $this->assertNotEmpty(UserRole::values());
    }

    public function test_enum_colors_are_consistent(): void
    {
        // Test that colors are valid Tailwind CSS color names
        $validColors = [
            'amber', 'blue', 'cyan', 'emerald', 'fuchsia', 'gray', 'green', 'indigo',
            'lime', 'orange', 'pink', 'purple', 'red', 'rose', 'sky', 'teal', 'violet',
            'yellow', 'zinc'
        ];

        foreach (ContentBlockStatus::cases() as $case) {
            $this->assertContains($case->getColor(), $validColors);
        }

        foreach (FormStatus::cases() as $case) {
            $this->assertContains($case->getColor(), $validColors);
        }

        foreach (NotificationType::cases() as $case) {
            $this->assertContains($case->getColor(), $validColors);
        }
    }

    public function test_enum_icons_are_heroicon_names(): void
    {
        // Test that icons are valid Heroicon names (basic validation)
        $validIconPattern = '/^[a-z-]+$/';

        foreach (ContentBlockStatus::cases() as $case) {
            $this->assertMatchesRegularExpression($validIconPattern, $case->getIcon());
        }

        foreach (FormStatus::cases() as $case) {
            $this->assertMatchesRegularExpression($validIconPattern, $case->getIcon());
        }

        foreach (NotificationType::cases() as $case) {
            $this->assertMatchesRegularExpression($validIconPattern, $case->getIcon());
        }
    }
} 