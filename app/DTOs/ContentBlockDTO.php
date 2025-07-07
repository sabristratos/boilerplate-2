<?php

declare(strict_types=1);

namespace App\DTOs;

use App\Enums\ContentBlockStatus;
use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * Data Transfer Object for Content Block data.
 *
 * This DTO encapsulates all data related to a content block, including
 * its type, data, settings, visibility, and ordering information.
 * It provides type-safe access to block properties and includes
 * validation and transformation methods.
 */
class ContentBlockDTO extends BaseDTO
{
    /**
     * Create a new ContentBlockDTO instance.
     *
     * @param int|null $id The block ID
     * @param string $type The block type identifier
     * @param int $pageId The page ID this block belongs to
     * @param array<string, mixed> $data The block data (translatable)
     * @param array<string, mixed> $settings The block settings
     * @param bool $visible Whether the block is visible
     * @param int $order The block order within the page
     * @param Carbon|null $createdAt When the block was created
     * @param Carbon|null $updatedAt When the block was last updated
     * @param array<string, mixed>|null $translations The translations for this block
     */
    public function __construct(
        public readonly ?int $id,
        public readonly string $type,
        public readonly int $pageId,
        public readonly array $data,
        public readonly array $settings,
        public readonly bool $visible,
        public readonly int $order,
        public readonly ?Carbon $createdAt = null,
        public readonly ?Carbon $updatedAt = null,
        public readonly ?array $translations = null,
    ) {
    }

    /**
     * Create a ContentBlockDTO from an array.
     *
     * @param array<string, mixed> $data The array data
     * @return self The created DTO
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            type: $data['type'],
            pageId: $data['page_id'],
            data: $data['data'] ?? [],
            settings: $data['settings'] ?? [],
            visible: $data['visible'] ?? true,
            order: $data['order'] ?? 0,
            createdAt: isset($data['created_at']) ? Carbon::parse($data['created_at']) : null,
            updatedAt: isset($data['updated_at']) ? Carbon::parse($data['updated_at']) : null,
            translations: $data['translations'] ?? null,
        );
    }

    /**
     * Create a ContentBlockDTO from a ContentBlock model.
     *
     * @param \App\Models\ContentBlock $block The content block model
     * @return self The created DTO
     */
    public static function fromModel(\App\Models\ContentBlock $block): self
    {
        return new self(
            id: $block->id,
            type: $block->type,
            pageId: $block->page_id,
            data: $block->data ?? [],
            settings: $block->settings ?? [],
            visible: $block->visible,
            order: $block->order,
            createdAt: $block->created_at,
            updatedAt: $block->updated_at,
            translations: $block->getTranslations('data'),
        );
    }

    /**
     * Create a ContentBlockDTO for creating a new block.
     *
     * @param string $type The block type
     * @param int $pageId The page ID
     * @param array<string, mixed> $data The block data
     * @param array<string, mixed> $settings The block settings
     * @param bool $visible Whether the block is visible
     * @param int $order The block order
     * @return self The created DTO
     */
    public static function forCreation(
        string $type,
        int $pageId,
        array $data = [],
        array $settings = [],
        bool $visible = true,
        int $order = 0,
    ): self {
        return new self(
            id: null,
            type: $type,
            pageId: $pageId,
            data: $data,
            settings: $settings,
            visible: $visible,
            order: $order,
        );
    }

    /**
     * Get the block data for a specific locale.
     *
     * @param string|null $locale The locale to get data for
     * @return array<string, mixed> The translated data
     */
    public function getDataForLocale(?string $locale = null): array
    {
        if ($locale === null || $this->translations === null) {
            return $this->data;
        }

        return $this->translations[$locale] ?? $this->data;
    }

    /**
     * Check if the block has data for a specific locale.
     *
     * @param string $locale The locale to check
     * @return bool True if data exists for the locale
     */
    public function hasDataForLocale(string $locale): bool
    {
        return $this->translations !== null && isset($this->translations[$locale]);
    }

    /**
     * Get all available locales for this block.
     *
     * @return array<string> The available locales
     */
    public function getAvailableLocales(): array
    {
        if ($this->translations === null) {
            return [];
        }

        return array_keys($this->translations);
    }

    /**
     * Get a specific setting value.
     *
     * @param string $key The setting key
     * @param mixed $default The default value if key doesn't exist
     * @return mixed The setting value
     */
    public function getSetting(string $key, mixed $default = null): mixed
    {
        return $this->settings[$key] ?? $default;
    }

    /**
     * Check if a setting exists.
     *
     * @param string $key The setting key
     * @return bool True if the setting exists
     */
    public function hasSetting(string $key): bool
    {
        return isset($this->settings[$key]);
    }

    /**
     * Get a specific data value.
     *
     * @param string $key The data key
     * @param mixed $default The default value if key doesn't exist
     * @return mixed The data value
     */
    public function getData(string $key, mixed $default = null): mixed
    {
        return $this->data[$key] ?? $default;
    }

    /**
     * Check if a data key exists.
     *
     * @param string $key The data key
     * @return bool True if the data key exists
     */
    public function hasData(string $key): bool
    {
        return isset($this->data[$key]);
    }

    /**
     * Check if this block is visible.
     *
     * @return bool True if the block is visible
     */
    public function isVisible(): bool
    {
        return $this->visible;
    }

    /**
     * Check if this block is hidden.
     *
     * @return bool True if the block is hidden
     */
    public function isHidden(): bool
    {
        return ! $this->visible;
    }

    /**
     * Get the block as an array.
     *
     * @return array<string, mixed> The block data as array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'page_id' => $this->pageId,
            'data' => $this->data,
            'settings' => $this->settings,
            'visible' => $this->visible,
            'order' => $this->order,
            'created_at' => $this->createdAt?->toISOString(),
            'updated_at' => $this->updatedAt?->toISOString(),
            'translations' => $this->translations,
        ];
    }



    /**
     * Create a copy of this DTO with updated values.
     *
     * @param array<string, mixed> $changes The changes to apply
     * @return self A new DTO with the changes applied
     */
    public function with(array $changes): self
    {
        return new self(
            id: $changes['id'] ?? $this->id,
            type: $changes['type'] ?? $this->type,
            pageId: $changes['page_id'] ?? $changes['pageId'] ?? $this->pageId,
            data: $changes['data'] ?? $this->data,
            settings: $changes['settings'] ?? $this->settings,
            visible: $changes['visible'] ?? $this->visible,
            order: $changes['order'] ?? $this->order,
            createdAt: $changes['created_at'] ?? $changes['createdAt'] ?? $this->createdAt,
            updatedAt: $changes['updated_at'] ?? $changes['updatedAt'] ?? $this->updatedAt,
            translations: $changes['translations'] ?? $this->translations,
        );
    }

    /**
     * Validate the DTO data.
     *
     * @return array<string, string> Validation errors, empty if valid
     */
    public function validate(): array
    {
        $errors = [];

        if (empty($this->type)) {
            $errors['type'] = 'Block type is required';
        }

        if ($this->pageId <= 0) {
            $errors['page_id'] = 'Valid page ID is required';
        }

        if ($this->order < 0) {
            $errors['order'] = 'Order must be non-negative';
        }

        return $errors;
    }


} 