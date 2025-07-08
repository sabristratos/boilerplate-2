<?php

declare(strict_types=1);

namespace App\Traits;

use App\Enums\ContentBlockStatus;
use App\Enums\FormElementType;
use App\Enums\FormStatus;
use App\Enums\MediaType;
use App\Enums\NotificationType;
use App\Enums\PublishStatus;
use App\Enums\SettingGroupKey;
use App\Enums\SettingType;
use App\Enums\UserRole;

/**
 * Trait providing helper methods for enum usage in Livewire components.
 * 
 * This trait provides convenient methods to access enum colors, icons,
 * descriptions, and other properties for consistent UI display.
 */
trait WithEnumHelpers
{
    /**
     * Get the status enum for content blocks.
     */
    public function getContentBlockStatus(string $status): ContentBlockStatus
    {
        return ContentBlockStatus::from($status);
    }

    /**
     * Get the status enum for forms.
     */
    public function getFormStatus(string $status): FormStatus
    {
        return FormStatus::from($status);
    }

    /**
     * Get the status enum for publishable content.
     */
    public function getPublishStatus(string $status): PublishStatus
    {
        return PublishStatus::from($status);
    }

    /**
     * Get the media type enum for a MIME type.
     */
    public function getMediaType(string $mimeType): MediaType
    {
        return MediaType::fromMimeType($mimeType);
    }

    /**
     * Get the notification type enum.
     */
    public function getNotificationType(string $type): NotificationType
    {
        return NotificationType::from($type);
    }

    /**
     * Get the user role enum.
     */
    public function getUserRole(string $role): UserRole
    {
        return UserRole::from($role);
    }

    /**
     * Get the setting group key enum.
     */
    public function getSettingGroupKey(string $key): SettingGroupKey
    {
        return SettingGroupKey::from($key);
    }

    /**
     * Get the setting type enum.
     */
    public function getSettingType(string $type): SettingType
    {
        return SettingType::from($type);
    }

    /**
     * Get the form element type enum.
     */
    public function getFormElementType(string $type): FormElementType
    {
        return FormElementType::from($type);
    }

    /**
     * Get all available status options for content blocks.
     */
    public function getContentBlockStatusOptions(): array
    {
        return ContentBlockStatus::options();
    }

    /**
     * Get all available status options for forms.
     */
    public function getFormStatusOptions(): array
    {
        return FormStatus::options();
    }

    /**
     * Get all available status options for publishable content.
     */
    public function getPublishStatusOptions(): array
    {
        return PublishStatus::options();
    }

    /**
     * Get all available media type options.
     */
    public function getMediaTypeOptions(): array
    {
        return MediaType::options();
    }

    /**
     * Get all available notification type options.
     */
    public function getNotificationTypeOptions(): array
    {
        return NotificationType::options();
    }

    /**
     * Get all available user role options.
     */
    public function getUserRoleOptions(): array
    {
        return UserRole::options();
    }

    /**
     * Get all available setting group key options.
     */
    public function getSettingGroupKeyOptions(): array
    {
        return SettingGroupKey::options();
    }

    /**
     * Get all available setting type options.
     */
    public function getSettingTypeOptions(): array
    {
        return SettingType::options();
    }

    /**
     * Get all available form element type options.
     */
    public function getFormElementTypeOptions(): array
    {
        return FormElementType::options();
    }

    /**
     * Get all available form element types as cases.
     */
    public function getFormElementTypes(): array
    {
        return FormElementType::cases();
    }

    /**
     * Get status badge data for content blocks.
     */
    public function getContentBlockStatusBadge(string $status): array
    {
        $statusEnum = $this->getContentBlockStatus($status);
        return [
            'color' => $statusEnum->getColor(),
            'icon' => $statusEnum->getIcon(),
            'label' => $statusEnum->label(),
            'description' => $statusEnum->getDescription(),
        ];
    }

    /**
     * Get status badge data for forms.
     */
    public function getFormStatusBadge(string $status): array
    {
        $statusEnum = $this->getFormStatus($status);
        return [
            'color' => $statusEnum->getColor(),
            'icon' => $statusEnum->getIcon(),
            'label' => $statusEnum->label(),
            'description' => $statusEnum->getDescription(),
        ];
    }

    /**
     * Get status badge data for publishable content.
     */
    public function getPublishStatusBadge(string $status): array
    {
        $statusEnum = $this->getPublishStatus($status);
        return [
            'color' => $statusEnum->getColor(),
            'icon' => $statusEnum->getIcon(),
            'label' => $statusEnum->label(),
            'description' => $statusEnum->getDescription(),
        ];
    }

    /**
     * Get media type badge data.
     */
    public function getMediaTypeBadge(string $mimeType): array
    {
        $mediaType = $this->getMediaType($mimeType);
        return [
            'color' => $mediaType->getColor(),
            'icon' => $mediaType->getIcon(),
            'label' => $mediaType->label(),
            'description' => $mediaType->getDescription(),
        ];
    }

    /**
     * Get notification type badge data.
     */
    public function getNotificationTypeBadge(string $type): array
    {
        $notificationType = $this->getNotificationType($type);
        return [
            'color' => $notificationType->getColor(),
            'icon' => $notificationType->getIcon(),
            'label' => $notificationType->label(),
            'description' => $notificationType->getDescription(),
        ];
    }

    /**
     * Get user role badge data.
     */
    public function getUserRoleBadge(string $role): array
    {
        $userRole = $this->getUserRole($role);
        return [
            'color' => $userRole->getColor(),
            'icon' => $userRole->getIcon(),
            'label' => $userRole->label(),
            'description' => $userRole->getDescription(),
        ];
    }

    /**
     * Get setting group badge data.
     */
    public function getSettingGroupBadge(string $key): array
    {
        $settingGroup = $this->getSettingGroupKey($key);
        return [
            'color' => $settingGroup->getColor(),
            'icon' => $settingGroup->getIcon(),
            'label' => $settingGroup->label(),
            'description' => $settingGroup->getDescription(),
        ];
    }

    /**
     * Get setting type badge data.
     */
    public function getSettingTypeBadge(string $type): array
    {
        $settingType = $this->getSettingType($type);
        return [
            'color' => $settingType->getColor(),
            'icon' => $settingType->getIcon(),
            'label' => $settingType->label(),
            'description' => $settingType->getDescription(),
        ];
    }

    /**
     * Get form element type badge data.
     */
    public function getFormElementTypeBadge(string $type): array
    {
        $elementType = $this->getFormElementType($type);
        return [
            'color' => $elementType->getColor(),
            'icon' => $elementType->getIcon(),
            'label' => $elementType->label(),
            'description' => $elementType->getDescription(),
        ];
    }
} 