<?php

declare(strict_types=1);

namespace App\DTOs;

use App\Enums\PublishStatus;
use Carbon\Carbon;

/**
 * Data Transfer Object for Page data.
 *
 * This DTO encapsulates all data related to a page, including
 * its title, slug, status, meta information, and SEO settings.
 * It provides type-safe access to page properties and includes
 * validation and transformation methods.
 */
class PageDTO extends BaseDTO
{
    /**
     * Create a new PageDTO instance.
     *
     * @param int|null $id The page ID
     * @param array<string, string> $title The page title (translatable)
     * @param string $slug The page slug
     * @param PublishStatus $status The page status
     * @param array<string, string>|null $metaTitle The meta title (translatable)
     * @param array<string, string>|null $metaDescription The meta description (translatable)
     * @param array<string, string>|null $metaKeywords The meta keywords (translatable)
     * @param array<string, string>|null $ogTitle The Open Graph title (translatable)
     * @param array<string, string>|null $ogDescription The Open Graph description (translatable)
     * @param array<string, string>|null $ogImage The Open Graph image (translatable)
     * @param array<string, string>|null $twitterTitle The Twitter title (translatable)
     * @param array<string, string>|null $twitterDescription The Twitter description (translatable)
     * @param array<string, string>|null $twitterImage The Twitter image (translatable)
     * @param array<string, string>|null $twitterCardType The Twitter card type (translatable)
     * @param array<string, string>|null $canonicalUrl The canonical URL (translatable)
     * @param array<string, mixed>|null $structuredData The structured data (translatable)
     * @param bool $noIndex Whether to prevent indexing
     * @param bool $noFollow Whether to prevent following links
     * @param bool $noArchive Whether to prevent archiving
     * @param bool $noSnippet Whether to prevent snippets
     * @param Carbon|null $createdAt When the page was created
     * @param Carbon|null $updatedAt When the page was last updated
     */
    public function __construct(
        public readonly ?int $id,
        public readonly array $title,
        public readonly string $slug,
        public readonly PublishStatus $status,
        public readonly ?array $metaTitle = null,
        public readonly ?array $metaDescription = null,
        public readonly ?array $metaKeywords = null,
        public readonly ?array $ogTitle = null,
        public readonly ?array $ogDescription = null,
        public readonly ?array $ogImage = null,
        public readonly ?array $twitterTitle = null,
        public readonly ?array $twitterDescription = null,
        public readonly ?array $twitterImage = null,
        public readonly ?array $twitterCardType = null,
        public readonly ?array $canonicalUrl = null,
        public readonly ?array $structuredData = null,
        public readonly bool $noIndex = false,
        public readonly bool $noFollow = false,
        public readonly bool $noArchive = false,
        public readonly bool $noSnippet = false,
        public readonly ?Carbon $createdAt = null,
        public readonly ?Carbon $updatedAt = null,
    ) {
    }

    /**
     * Create a PageDTO from an array.
     *
     * @param array<string, mixed> $data The array data
     * @return self The created DTO
     */
    public static function fromArray(array $data): static
    {
        return new static(
            id: $data['id'] ?? null,
            title: $data['title'] ?? [],
            slug: $data['slug'],
            status: isset($data['status'])
                ? ($data['status'] instanceof PublishStatus ? $data['status'] : PublishStatus::from($data['status']))
                : PublishStatus::DRAFT,
            metaTitle: $data['meta_title'] ?? null,
            metaDescription: $data['meta_description'] ?? null,
            metaKeywords: $data['meta_keywords'] ?? null,
            ogTitle: $data['og_title'] ?? null,
            ogDescription: $data['og_description'] ?? null,
            ogImage: $data['og_image'] ?? null,
            twitterTitle: $data['twitter_title'] ?? null,
            twitterDescription: $data['twitter_description'] ?? null,
            twitterImage: $data['twitter_image'] ?? null,
            twitterCardType: $data['twitter_card_type'] ?? null,
            canonicalUrl: $data['canonical_url'] ?? null,
            structuredData: $data['structured_data'] ?? null,
            noIndex: $data['no_index'] ?? false,
            noFollow: $data['no_follow'] ?? false,
            noArchive: $data['no_archive'] ?? false,
            noSnippet: $data['no_snippet'] ?? false,
            createdAt: isset($data['created_at']) ? Carbon::parse($data['created_at']) : null,
            updatedAt: isset($data['updated_at']) ? Carbon::parse($data['updated_at']) : null,
        );
    }

    /**
     * Create a PageDTO from a Page model.
     *
     * @param \App\Models\Page $page The page model
     * @return self The created DTO
     */
    public static function fromModel(\App\Models\Page $page): self
    {
        return new self(
            id: $page->id,
            title: $page->getTranslations('title'),
            slug: $page->slug,
            status: $page->status,
            metaTitle: $page->getTranslations('meta_title'),
            metaDescription: $page->getTranslations('meta_description'),
            metaKeywords: $page->getTranslations('meta_keywords'),
            ogTitle: $page->getTranslations('og_title'),
            ogDescription: $page->getTranslations('og_description'),
            ogImage: $page->getTranslations('og_image'),
            twitterTitle: $page->getTranslations('twitter_title'),
            twitterDescription: $page->getTranslations('twitter_description'),
            twitterImage: $page->getTranslations('twitter_image'),
            twitterCardType: $page->getTranslations('twitter_card_type'),
            canonicalUrl: $page->getTranslations('canonical_url'),
            structuredData: $page->getTranslations('structured_data'),
            noIndex: $page->no_index,
            noFollow: $page->no_follow,
            noArchive: $page->no_archive,
            noSnippet: $page->no_snippet,
            createdAt: $page->created_at,
            updatedAt: $page->updated_at,
        );
    }

    /**
     * Create a PageDTO for creating a new page.
     *
     * @param array<string, string> $title The page title
     * @param string $slug The page slug
     * @param PublishStatus $status The page status
     * @param array<string, mixed> $metaData The meta data
     * @return self The created DTO
     */
    public static function forCreation(
        array $title,
        string $slug,
        PublishStatus $status = PublishStatus::DRAFT,
        array $metaData = [],
    ): self {
        return new self(
            id: null,
            title: $title,
            slug: $slug,
            status: $status,
            metaTitle: $metaData['meta_title'] ?? null,
            metaDescription: $metaData['meta_description'] ?? null,
            metaKeywords: $metaData['meta_keywords'] ?? null,
            ogTitle: $metaData['og_title'] ?? null,
            ogDescription: $metaData['og_description'] ?? null,
            ogImage: $metaData['og_image'] ?? null,
            twitterTitle: $metaData['twitter_title'] ?? null,
            twitterDescription: $metaData['twitter_description'] ?? null,
            twitterImage: $metaData['twitter_image'] ?? null,
            twitterCardType: $metaData['twitter_card_type'] ?? null,
            canonicalUrl: $metaData['canonical_url'] ?? null,
            structuredData: $metaData['structured_data'] ?? null,
            noIndex: $metaData['no_index'] ?? false,
            noFollow: $metaData['no_follow'] ?? false,
            noArchive: $metaData['no_archive'] ?? false,
            noSnippet: $metaData['no_snippet'] ?? false,
        );
    }

    /**
     * Get the title for a specific locale.
     *
     * @param string|null $locale The locale to get title for
     * @return string|null The translated title
     */
    public function getTitleForLocale(?string $locale = null): ?string
    {
        if ($locale === null) {
            $locale = app()->getLocale() ?: config('app.fallback_locale', 'en');
        }

        return $this->title[$locale] ?? null;
    }

    /**
     * Get the meta title for a specific locale.
     *
     * @param string|null $locale The locale to get meta title for
     * @return string|null The translated meta title
     */
    public function getMetaTitleForLocale(?string $locale = null): ?string
    {
        if ($locale === null) {
            $locale = app()->getLocale() ?: config('app.fallback_locale', 'en');
        }

        return $this->metaTitle[$locale] ?? null;
    }

    /**
     * Get the meta description for a specific locale.
     *
     * @param string|null $locale The locale to get meta description for
     * @return string|null The translated meta description
     */
    public function getMetaDescriptionForLocale(?string $locale = null): ?string
    {
        if ($locale === null) {
            $locale = app()->getLocale() ?: config('app.fallback_locale', 'en');
        }

        return $this->metaDescription[$locale] ?? null;
    }

    /**
     * Check if the page has a title for a specific locale.
     *
     * @param string $locale The locale to check
     * @return bool True if title exists for the locale
     */
    public function hasTitleForLocale(string $locale): bool
    {
        return isset($this->title[$locale]);
    }

    /**
     * Get all available locales for this page.
     *
     * @return array<string> The available locales
     */
    public function getAvailableLocales(): array
    {
        return array_keys($this->title);
    }

    /**
     * Check if the page is published.
     *
     * @return bool True if the page is published
     */
    public function isPublished(): bool
    {
        return $this->status === PublishStatus::PUBLISHED;
    }

    /**
     * Check if the page is a draft.
     *
     * @return bool True if the page is a draft
     */
    public function isDraft(): bool
    {
        return $this->status === PublishStatus::DRAFT;
    }

    /**
     * Check if the page is scheduled.
     *
     * @return bool True if the page is scheduled
     */
    public function isScheduled(): bool
    {
        return $this->status === PublishStatus::Scheduled;
    }

    /**
     * Check if the page should be indexed by search engines.
     *
     * @return bool True if the page should be indexed
     */
    public function shouldBeIndexed(): bool
    {
        return ! $this->noIndex;
    }

    /**
     * Check if the page links should be followed.
     *
     * @return bool True if links should be followed
     */
    public function shouldFollowLinks(): bool
    {
        return ! $this->noFollow;
    }

    /**
     * Get the page as an array.
     *
     * @return array<string, mixed> The page data as array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'status' => $this->status->value,
            'meta_title' => $this->metaTitle,
            'meta_description' => $this->metaDescription,
            'meta_keywords' => $this->metaKeywords,
            'og_title' => $this->ogTitle,
            'og_description' => $this->ogDescription,
            'og_image' => $this->ogImage,
            'twitter_title' => $this->twitterTitle,
            'twitter_description' => $this->twitterDescription,
            'twitter_image' => $this->twitterImage,
            'twitter_card_type' => $this->twitterCardType,
            'canonical_url' => $this->canonicalUrl,
            'structured_data' => $this->structuredData,
            'no_index' => $this->noIndex,
            'no_follow' => $this->noFollow,
            'no_archive' => $this->noArchive,
            'no_snippet' => $this->noSnippet,
            'created_at' => $this->createdAt?->toISOString(),
            'updated_at' => $this->updatedAt?->toISOString(),
        ];
    }



    /**
     * Create a copy of this DTO with updated values.
     *
     * @param array<string, mixed> $changes The changes to apply
     * @return static A new DTO with the changes applied
     */
    public function with(array $changes): static
    {
        return new self(
            id: $changes['id'] ?? $this->id,
            title: $changes['title'] ?? $this->title,
            slug: $changes['slug'] ?? $this->slug,
            status: $changes['status'] ?? $this->status,
            metaTitle: $changes['meta_title'] ?? $this->metaTitle,
            metaDescription: $changes['meta_description'] ?? $this->metaDescription,
            metaKeywords: $changes['meta_keywords'] ?? $this->metaKeywords,
            ogTitle: $changes['og_title'] ?? $this->ogTitle,
            ogDescription: $changes['og_description'] ?? $this->ogDescription,
            ogImage: $changes['og_image'] ?? $this->ogImage,
            twitterTitle: $changes['twitter_title'] ?? $this->twitterTitle,
            twitterDescription: $changes['twitter_description'] ?? $this->twitterDescription,
            twitterImage: $changes['twitter_image'] ?? $this->twitterImage,
            twitterCardType: $changes['twitter_card_type'] ?? $this->twitterCardType,
            canonicalUrl: $changes['canonical_url'] ?? $this->canonicalUrl,
            structuredData: $changes['structured_data'] ?? $this->structuredData,
            noIndex: $changes['no_index'] ?? $this->noIndex,
            noFollow: $changes['no_follow'] ?? $this->noFollow,
            noArchive: $changes['no_archive'] ?? $this->noArchive,
            noSnippet: $changes['no_snippet'] ?? $this->noSnippet,
            createdAt: $changes['created_at'] ?? $this->createdAt,
            updatedAt: $changes['updated_at'] ?? $this->updatedAt,
        );
    }

    /**
     * Validate the DTO data.
     *
     * @return array<string, string> Validation errors, empty if valid
     */
    public function validate(): array
    {
        $validationService = app(\App\Services\DTOValidationService::class);
        
        // Get validation rules
        $rules = $validationService->getPageDataRules();
        
        // Add ID validation for updates
        if ($this->id !== null) {
            $rules['id'] = 'required|integer|min:1';
        }
        
        // Get custom messages and attributes
        $messages = $validationService->getCustomValidationMessages();
        $attributes = $validationService->getCustomAttributeNames();
        
        // Validate using the service
        $errors = $validationService->validateDTO($this, $rules, $messages, $attributes);
        
        // Add custom validation for translatable fields
        $titleErrors = $validationService->validateTranslatableField($this->toArray(), 'title');
        $errors = array_merge($errors, $titleErrors);
        
        // Add custom validation for meta fields
        $metaFields = ['meta_title', 'meta_description', 'meta_keywords', 'og_title', 'og_description', 'twitter_title', 'twitter_description'];
        foreach ($metaFields as $field) {
            if (!empty($this->toArray()[$field])) {
                $metaErrors = $validationService->validateTranslatableField($this->toArray(), $field, false);
                $errors = array_merge($errors, $metaErrors);
            }
        }
        
        return $errors;
    }


} 