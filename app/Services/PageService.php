<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\PageDTO;
use App\DTOs\DTOFactory;
use App\Enums\PublishStatus;
use App\Models\Page;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;

/**
 * Service for handling page operations using DTOs.
 *
 * This service provides type-safe page management operations
 * using Data Transfer Objects for data integrity and consistency.
 */
class PageService
{
    /**
     * Create a new page.
     *
     * @param PageDTO $pageDto The page data
     * @return Page The created page
     * @throws InvalidArgumentException If the DTO is invalid
     */
    public function createPage(PageDTO $pageDto): Page
    {
        if (!$pageDto->isValid()) {
            throw new InvalidArgumentException('Invalid page data: ' . $pageDto->getValidationErrorsAsString());
        }

        try {
            DB::beginTransaction();

            $page = new Page();
            $this->fillPageFromDTO($page, $pageDto);
            $page->save();

            DB::commit();

            Log::info('Page created successfully', [
                'page_id' => $page->id,
                'slug' => $page->slug,
            ]);

            return $page;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create page', [
                'error' => $e->getMessage(),
                'page_data' => $pageDto->toArray(),
            ]);
            throw $e;
        }
    }



    /**
     * Delete a page.
     *
     * @param Page $page The page to delete
     * @return bool True if deleted successfully
     */
    public function deletePage(Page $page): bool
    {
        try {
            DB::beginTransaction();

            $page->delete();

            DB::commit();

            Log::info('Page deleted successfully', [
                'page_id' => $page->id,
                'slug' => $page->slug,
            ]);

            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete page', [
                'page_id' => $page->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Get a page by ID.
     *
     * @param int $id The page ID
     * @return PageDTO|null The page DTO or null if not found
     */
    public function getPageById(int $id): ?PageDTO
    {
        $page = Page::find($id);
        if (!$page) {
            return null;
        }
        return DTOFactory::createPageDTO($page);
    }

    /**
     * Get all pages with pagination.
     *
     * @param int $perPage The number of pages per page
     * @param array<string, mixed> $filters Optional filters
     * @return LengthAwarePaginator The paginated pages
     */
    public function getPagesPaginated(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = Page::query();

        // Apply filters
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $locale = $filters['locale'] ?? app()->getLocale() ?? 'en';
            $query->where(fn ($q) => $q->where('slug', 'like', "%$search%")
                ->orWhereRaw("JSON_EXTRACT(title, '$.\"{$locale}\"') LIKE ?", ["%{$search}%"])
            );
        }

        $pages = $query->orderBy('title')->paginate($perPage);

        // Convert to DTOs
        $pages->getCollection()->transform(fn($page): \App\DTOs\PageDTO => DTOFactory::createPageDTO($page));

        return $pages;
    }

    /**
     * Validate page data against DTO rules.
     *
     * @param PageDTO $pageDto The page DTO
     * @return array<string, string> Validation errors, empty if valid
     */
    public function validatePageData(PageDTO $pageDto): array
    {
        return $pageDto->validate();
    }

    /**
     * Fill a Page model from a PageDTO.
     *
     * @param Page $page The page model
     * @param PageDTO $dto The DTO
     */
    private function fillPageFromDTO(Page $page, PageDTO $dto): void
    {
        $page->setTranslations('title', $dto->title);
        $page->slug = $dto->slug;
        $page->status = $dto->status;
        $page->setTranslations('meta_title', $dto->metaTitle ?? []);
        $page->setTranslations('meta_description', $dto->metaDescription ?? []);
        $page->setTranslations('meta_keywords', $dto->metaKeywords ?? []);
        $page->setTranslations('og_title', $dto->ogTitle ?? []);
        $page->setTranslations('og_description', $dto->ogDescription ?? []);
        $page->setTranslations('og_image', $dto->ogImage ?? []);
        $page->setTranslations('twitter_title', $dto->twitterTitle ?? []);
        $page->setTranslations('twitter_description', $dto->twitterDescription ?? []);
        $page->setTranslations('twitter_image', $dto->twitterImage ?? []);
        $page->setTranslations('twitter_card_type', $dto->twitterCardType ?? []);
        $page->setTranslations('canonical_url', $dto->canonicalUrl ?? []);
        $page->setTranslations('structured_data', $dto->structuredData ?? []);
        $page->no_index = $dto->noIndex;
        $page->no_follow = $dto->noFollow;
        $page->no_archive = $dto->noArchive;
        $page->no_snippet = $dto->noSnippet;
    }

    /**
     * Update a page with revision support.
     *
     * @param Page $page The page to update
     * @param PageDTO $pageDto The updated page data
     * @param string $revisionType The type of revision
     * @param string $revisionMessage The revision message
     * @param bool $isPublished Whether this is a published revision
     * @return Page The updated page
     */
    public function updatePage(Page $page, PageDTO $pageDto, string $revisionType = 'update', string $revisionMessage = '', bool $isPublished = false): Page
    {
        if (!$pageDto->isValid()) {
            throw new InvalidArgumentException('Invalid page data: ' . $pageDto->getValidationErrorsAsString());
        }

        try {
            DB::beginTransaction();

            $this->fillPageFromDTO($page, $pageDto);
            $page->save();

            // Create revision if needed
            if ($revisionType && $revisionMessage) {
                $page->createManualRevision($revisionType, $revisionMessage, [], $isPublished);
            }

            DB::commit();

            Log::info('Page updated successfully', [
                'page_id' => $page->id,
                'slug' => $page->slug,
                'revision_type' => $revisionType,
            ]);

            return $page;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update page', [
                'page_id' => $page->id,
                'error' => $e->getMessage(),
                'page_data' => $pageDto->toArray(),
            ]);
            throw $e;
        }
    }

    /**
     * Get pages with filters for the admin interface.
     *
     * @param string $search Search term
     * @param array<string, mixed> $filters Additional filters
     * @param string $sortBy Sort column
     * @param string $sortDirection Sort direction
     * @param int $perPage Items per page
     * @return LengthAwarePaginator The paginated pages
     */
    public function getPagesWithFilters(
        string $search = '',
        array $filters = [],
        string $sortBy = 'title',
        string $sortDirection = 'asc',
        int $perPage = 10
    ): LengthAwarePaginator {
        $query = Page::query();

        // Apply search
        if ($search !== '' && $search !== '0') {
            $locale = $filters['locale'] ?? app()->getLocale() ?? 'en';
            $query->where(fn ($q) => $q->where('slug', 'like', "%$search%")
                ->orWhereRaw("JSON_EXTRACT(title, '$.\"{$locale}\"') LIKE ?", ["%{$search}%"])
            );
        }

        // Apply additional filters
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Apply sorting
        $query->orderBy($sortBy, $sortDirection);

        return $query->paginate($perPage);
    }

    /**
     * Get a page with all its content blocks.
     *
     * @param Page $page The page
     * @return Page The page with content blocks loaded
     */
    public function getPageWithContent(Page $page): Page
    {
        return $page->load(['contentBlocks' => function ($query): void {
            $query->ordered();
        }]);
    }

    /**
     * Generate a unique slug for a page.
     *
     * @param string $baseSlug The base slug
     * @return string The unique slug
     */
    public function generateUniqueSlug(string $baseSlug): string
    {
        $slug = $baseSlug;
        $counter = 1;

        while (Page::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Get pages by status.
     *
     * @param string $status The status to filter by
     * @return Collection The pages with the specified status
     */
    public function getPagesByStatus(string $status): Collection
    {
        return Page::where('status', $status)->get();
    }

    /**
     * Search pages by title or slug.
     *
     * @param string $searchTerm The search term
     * @param string|null $locale The locale to search in
     * @return Collection The matching pages
     */
    public function searchPages(string $searchTerm, ?string $locale = null): Collection
    {
        $locale ??= app()->getLocale() ?? 'en';
        
        return Page::where('slug', 'like', "%$searchTerm%")
            ->orWhereRaw("JSON_EXTRACT(title, '$.\"{$locale}\"') LIKE ?", ["%{$searchTerm}%"])
            ->get();
    }

    /**
     * Get a page by slug.
     *
     * @param string $slug The page slug
     * @return Page|null The page or null if not found
     */
    public function getPageBySlug(string $slug): ?Page
    {
        return Page::where('slug', $slug)->first();
    }
} 