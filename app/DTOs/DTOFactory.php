<?php

declare(strict_types=1);

namespace App\DTOs;

use App\Models\ContentBlock;
use App\Models\Form;
use App\Models\FormSubmission;
use App\Models\Page;
use App\Models\User;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * Factory class for creating DTOs from various data sources.
 *
 * This factory provides a centralized way to create DTOs from models,
 * arrays, and other data sources. It ensures consistent DTO creation
 * across the application.
 */
class DTOFactory
{
    /**
     * Create a ContentBlockDTO from various data sources.
     *
     * @param ContentBlock|array<string, mixed> $data The data source
     * @return ContentBlockDTO The created DTO
     */
    public static function createContentBlockDTO(ContentBlock|array $data): ContentBlockDTO
    {
        if ($data instanceof ContentBlock) {
            return ContentBlockDTO::fromModel($data);
        }

        return ContentBlockDTO::fromArray($data);
    }

    /**
     * Create a PageDTO from various data sources.
     *
     * @param \App\Models\Page|array<string, mixed> $data The data source
     * @return PageDTO The created DTO
     */
    public static function createPageDTO(\App\Models\Page|array $data): PageDTO
    {
        if ($data instanceof \App\Models\Page) {
            return PageDTO::fromModel($data);
        }
        return PageDTO::fromArray($data);
    }

    /**
     * Create multiple PageDTOs from a collection.
     *
     * @param \Illuminate\Support\Collection<int, \App\Models\Page>|array<\App\Models\Page> $collection The collection
     * @return array<PageDTO> The created DTOs
     */
    public static function createPageDTOs(\Illuminate\Support\Collection|array $collection): array
    {
        $dtos = [];
        foreach ($collection as $item) {
            $dtos[] = self::createPageDTO($item);
        }
        return $dtos;
    }

    /**
     * Create a FormDTO from various data sources.
     *
     * @param Form|array<string, mixed> $data The data source
     * @return FormDTO The created DTO
     */
    public static function createFormDTO(Form|array $data): FormDTO
    {
        if ($data instanceof Form) {
            return FormDTO::fromModel($data);
        }

        return FormDTO::fromArray($data);
    }

    /**
     * Create a FormSubmissionDTO from various data sources.
     *
     * @param FormSubmission|array<string, mixed> $data The data source
     * @return FormSubmissionDTO The created DTO
     */
    public static function createFormSubmissionDTO(FormSubmission|array $data): FormSubmissionDTO
    {
        if ($data instanceof FormSubmission) {
            return FormSubmissionDTO::fromModel($data);
        }

        return FormSubmissionDTO::fromArray($data);
    }

    /**
     * Create a UserDTO from various data sources.
     *
     * @param User|array<string, mixed> $data The data source
     * @return UserDTO The created DTO
     */
    public static function createUserDTO(User|array $data): UserDTO
    {
        if ($data instanceof User) {
            return UserDTO::fromModel($data);
        }

        return UserDTO::fromArray($data);
    }

    /**
     * Create a MediaDTO from various data sources.
     *
     * @param Media|array<string, mixed> $data The data source
     * @return MediaDTO The created DTO
     */
    public static function createMediaDTO(Media|array $data): MediaDTO
    {
        if ($data instanceof Media) {
            return MediaDTO::fromModel($data);
        }

        return MediaDTO::fromArray($data);
    }

    /**
     * Create multiple ContentBlockDTOs from a collection.
     *
     * @param \Illuminate\Support\Collection<int, ContentBlock>|array<ContentBlock> $collection The collection
     * @return array<ContentBlockDTO> The created DTOs
     */
    public static function createContentBlockDTOs(\Illuminate\Support\Collection|array $collection): array
    {
        $dtos = [];
        
        foreach ($collection as $item) {
            $dtos[] = self::createContentBlockDTO($item);
        }
        
        return $dtos;
    }

    /**
     * Create multiple FormDTOs from a collection.
     *
     * @param \Illuminate\Support\Collection<int, Form>|array<Form> $collection The collection
     * @return array<FormDTO> The created DTOs
     */
    public static function createFormDTOs(\Illuminate\Support\Collection|array $collection): array
    {
        $dtos = [];
        
        foreach ($collection as $item) {
            $dtos[] = self::createFormDTO($item);
        }
        
        return $dtos;
    }

    /**
     * Create multiple FormSubmissionDTOs from a collection.
     *
     * @param \Illuminate\Support\Collection<int, FormSubmission>|array<FormSubmission> $collection The collection
     * @return array<FormSubmissionDTO> The created DTOs
     */
    public static function createFormSubmissionDTOs(\Illuminate\Support\Collection|array $collection): array
    {
        $dtos = [];
        
        foreach ($collection as $item) {
            $dtos[] = self::createFormSubmissionDTO($item);
        }
        
        return $dtos;
    }

    /**
     * Create multiple UserDTOs from a collection.
     *
     * @param \Illuminate\Support\Collection<int, User>|array<User> $collection The collection
     * @return array<UserDTO> The created DTOs
     */
    public static function createUserDTOs(\Illuminate\Support\Collection|array $collection): array
    {
        $dtos = [];
        
        foreach ($collection as $item) {
            $dtos[] = self::createUserDTO($item);
        }
        
        return $dtos;
    }

    /**
     * Create multiple MediaDTOs from a collection.
     *
     * @param \Illuminate\Support\Collection<int, Media>|array<Media> $collection The collection
     * @return array<MediaDTO> The created DTOs
     */
    public static function createMediaDTOs(\Illuminate\Support\Collection|array $collection): array
    {
        $dtos = [];
        
        foreach ($collection as $item) {
            $dtos[] = self::createMediaDTO($item);
        }
        
        return $dtos;
    }

    /**
     * Create a ContentBlockDTO for creation.
     *
     * @param string $type The block type
     * @param int $pageId The page ID
     * @param array<string, mixed> $data The block data
     * @param array<string, mixed> $settings The block settings
     * @param bool $visible Whether the block is visible
     * @param int $order The block order
     * @return ContentBlockDTO The created DTO
     */
    public static function createContentBlockDTOForCreation(
        string $type,
        int $pageId,
        array $data = [],
        array $settings = [],
        bool $visible = true,
        int $order = 0,
    ): ContentBlockDTO {
        return ContentBlockDTO::forCreation($type, $pageId, $data, $settings, $visible, $order);
    }

    /**
     * Create a PageDTO for creation.
     *
     * @param array<string, string> $title The page title
     * @param string $slug The page slug
     * @param \App\Enums\PublishStatus $status The page status
     * @param array<string, mixed> $metaData The meta data
     * @return PageDTO The created DTO
     */
    public static function createPageDTOForCreation(
        array $title,
        string $slug,
        \App\Enums\PublishStatus $status = \App\Enums\PublishStatus::Draft,
        array $metaData = [],
    ): PageDTO {
        return PageDTO::forCreation($title, $slug, $status, $metaData);
    }

    /**
     * Create a FormDTO for creation.
     *
     * @param array<string, string> $name The form name
     * @param array<string, mixed> $elements The form elements
     * @param array<string, mixed> $settings The form settings
     * @param int|null $userId The user ID
     * @return FormDTO The created DTO
     */
    public static function createFormDTOForCreation(
        array $name,
        array $elements = [],
        array $settings = [],
        ?int $userId = null,
    ): FormDTO {
        return FormDTO::forCreation($name, $elements, $settings, $userId);
    }

    /**
     * Create a FormSubmissionDTO for creation.
     *
     * @param int $formId The form ID
     * @param array<string, mixed> $data The form data
     * @param string $ipAddress The IP address
     * @param string $userAgent The user agent
     * @return FormSubmissionDTO The created DTO
     */
    public static function createFormSubmissionDTOForCreation(
        int $formId,
        array $data,
        string $ipAddress,
        string $userAgent,
    ): FormSubmissionDTO {
        return FormSubmissionDTO::forCreation($formId, $data, $ipAddress, $userAgent);
    }

    /**
     * Create a UserDTO for creation.
     *
     * @param string $name The user's name
     * @param string $email The user's email
     * @param string|null $password The password
     * @param string|null $locale The user's preferred locale
     * @return UserDTO The created DTO
     */
    public static function createUserDTOForCreation(
        string $name,
        string $email,
        ?string $password = null,
        ?string $locale = null,
    ): UserDTO {
        return UserDTO::forCreation($name, $email, $password, $locale);
    }

    /**
     * Create a MediaDTO for creation.
     *
     * @param string $fileName The original file name
     * @param string $name The media name
     * @param string $mimeType The MIME type
     * @param int $size The file size
     * @param string $disk The storage disk
     * @param string $path The file path
     * @param string $collectionName The collection name
     * @param string $modelType The model type
     * @param int|null $modelId The model ID
     * @return MediaDTO The created DTO
     */
    public static function createMediaDTOForCreation(
        string $fileName,
        string $name,
        string $mimeType,
        int $size,
        string $disk,
        string $path,
        string $collectionName,
        string $modelType,
        ?int $modelId = null,
    ): MediaDTO {
        return MediaDTO::forCreation($fileName, $name, $mimeType, $size, $disk, $path, $collectionName, $modelType, $modelId);
    }
} 