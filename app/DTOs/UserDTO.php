<?php

declare(strict_types=1);

namespace App\DTOs;

use Carbon\Carbon;

/**
 * Data Transfer Object for User data.
 *
 * This DTO encapsulates all data related to a user, including
 * authentication information, profile data, and social login details.
 * It provides type-safe access to user properties and includes
 * validation and transformation methods.
 */
class UserDTO extends BaseDTO
{
    /**
     * Create a new UserDTO instance.
     *
     * @param int|null $id The user ID
     * @param string $name The user's name
     * @param string $email The user's email address
     * @param string|null $password The hashed password
     * @param string|null $locale The user's preferred locale
     * @param string|null $googleId The Google OAuth ID
     * @param string|null $googleToken The Google OAuth token
     * @param string|null $googleRefreshToken The Google OAuth refresh token
     * @param string|null $facebookId The Facebook OAuth ID
     * @param string|null $facebookToken The Facebook OAuth token
     * @param string|null $facebookRefreshToken The Facebook OAuth refresh token
     * @param Carbon|null $emailVerifiedAt When the email was verified
     * @param Carbon|null $createdAt When the user was created
     * @param Carbon|null $updatedAt When the user was last updated
     * @param array<string>|null $roles The user's roles
     * @param array<string>|null $permissions The user's permissions
     */
    public function __construct(
        public readonly ?int $id,
        public readonly string $name,
        public readonly string $email,
        public readonly ?string $password = null,
        public readonly ?string $locale = null,
        public readonly ?string $googleId = null,
        public readonly ?string $googleToken = null,
        public readonly ?string $googleRefreshToken = null,
        public readonly ?string $facebookId = null,
        public readonly ?string $facebookToken = null,
        public readonly ?string $facebookRefreshToken = null,
        public readonly ?Carbon $emailVerifiedAt = null,
        public readonly ?Carbon $createdAt = null,
        public readonly ?Carbon $updatedAt = null,
        public readonly ?array $roles = null,
        public readonly ?array $permissions = null,
        public readonly ?string $passwordConfirmation = null,
    ) {
    }

    /**
     * Create a UserDTO from an array.
     *
     * @param array<string, mixed> $data The array data
     * @return self The created DTO
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            name: $data['name'],
            email: $data['email'],
            password: $data['password'] ?? null,
            locale: $data['locale'] ?? null,
            googleId: $data['google_id'] ?? null,
            googleToken: $data['google_token'] ?? null,
            googleRefreshToken: $data['google_refresh_token'] ?? null,
            facebookId: $data['facebook_id'] ?? null,
            facebookToken: $data['facebook_token'] ?? null,
            facebookRefreshToken: $data['facebook_refresh_token'] ?? null,
            emailVerifiedAt: isset($data['email_verified_at']) ? Carbon::parse($data['email_verified_at']) : null,
            createdAt: isset($data['created_at']) ? Carbon::parse($data['created_at']) : null,
            updatedAt: isset($data['updated_at']) ? Carbon::parse($data['updated_at']) : null,
            roles: $data['roles'] ?? null,
            permissions: $data['permissions'] ?? null,
        );
    }

    /**
     * Create a UserDTO from a User model.
     *
     * @param \App\Models\User $user The user model
     * @return self The created DTO
     */
    public static function fromModel(\App\Models\User $user): self
    {
        return new self(
            id: $user->id,
            name: $user->name,
            email: $user->email,
            password: $user->password,
            locale: $user->locale,
            googleId: $user->google_id,
            googleToken: $user->google_token,
            googleRefreshToken: $user->google_refresh_token,
            facebookId: $user->facebook_id,
            facebookToken: $user->facebook_token,
            facebookRefreshToken: $user->facebook_refresh_token,
            emailVerifiedAt: $user->email_verified_at,
            createdAt: $user->created_at,
            updatedAt: $user->updated_at,
            roles: $user->roles->pluck('name')->toArray(),
            permissions: $user->permissions->pluck('name')->toArray(),
        );
    }

    /**
     * Create a UserDTO for creating a new user.
     *
     * @param string $name The user's name
     * @param string $email The user's email
     * @param string|null $password The password (will be hashed)
     * @param string|null $locale The user's preferred locale
     * @return self The created DTO
     */
    public static function forCreation(
        string $name,
        string $email,
        ?string $password = null,
        ?string $locale = null,
    ): self {
        return new self(
            id: null,
            name: $name,
            email: $email,
            password: $password,
            locale: $locale,
        );
    }

    /**
     * Get the user's initials.
     *
     * @return string The user's initials
     */
    public function getInitials(): string
    {
        $words = explode(' ', $this->name);
        $initials = '';
        
        foreach (array_slice($words, 0, 2) as $word) {
            $initials .= substr($word, 0, 1);
        }
        
        return strtoupper($initials);
    }

    /**
     * Check if the user has a social login account.
     *
     * @return bool True if the user has social login
     */
    public function hasSocialLogin(): bool
    {
        return $this->googleId !== null && $this->googleId !== '' && $this->googleId !== '0' || $this->facebookId !== null && $this->facebookId !== '' && $this->facebookId !== '0';
    }

    /**
     * Check if the user has a Google account.
     *
     * @return bool True if the user has Google login
     */
    public function hasGoogleLogin(): bool
    {
        return $this->googleId !== null && $this->googleId !== '' && $this->googleId !== '0';
    }

    /**
     * Check if the user has a Facebook account.
     *
     * @return bool True if the user has Facebook login
     */
    public function hasFacebookLogin(): bool
    {
        return $this->facebookId !== null && $this->facebookId !== '' && $this->facebookId !== '0';
    }

    /**
     * Check if the user has a password set.
     *
     * @return bool True if the user has a password
     */
    public function hasPassword(): bool
    {
        return $this->password !== null && $this->password !== '' && $this->password !== '0';
    }

    /**
     * Check if the user's email is verified.
     *
     * @return bool True if the email is verified
     */
    public function isEmailVerified(): bool
    {
        return $this->emailVerifiedAt instanceof \Carbon\Carbon;
    }

    /**
     * Check if the user has a specific role.
     *
     * @param string $roleName The role name to check
     * @return bool True if the user has the role
     */
    public function hasRole(string $roleName): bool
    {
        return $this->roles !== null && in_array($roleName, $this->roles, true);
    }

    /**
     * Check if the user has any of the specified roles.
     *
     * @param array<string> $roleNames The role names to check
     * @return bool True if the user has any of the roles
     */
    public function hasAnyRole(array $roleNames): bool
    {
        if ($this->roles === null) {
            return false;
        }
        
        return array_intersect($roleNames, $this->roles) !== [];
    }

    /**
     * Check if the user has all of the specified roles.
     *
     * @param array<string> $roleNames The role names to check
     * @return bool True if the user has all of the roles
     */
    public function hasAllRoles(array $roleNames): bool
    {
        if ($this->roles === null) {
            return false;
        }
        
        return array_diff($roleNames, $this->roles) === [];
    }

    /**
     * Check if the user has a specific permission.
     *
     * @param string $permissionName The permission name to check
     * @return bool True if the user has the permission
     */
    public function hasPermission(string $permissionName): bool
    {
        return $this->permissions !== null && in_array($permissionName, $this->permissions, true);
    }

    /**
     * Check if the user has any of the specified permissions.
     *
     * @param array<string> $permissionNames The permission names to check
     * @return bool True if the user has any of the permissions
     */
    public function hasAnyPermission(array $permissionNames): bool
    {
        if ($this->permissions === null) {
            return false;
        }
        
        return array_intersect($permissionNames, $this->permissions) !== [];
    }

    /**
     * Get the user's display name (first name only).
     *
     * @return string The user's display name
     */
    public function getDisplayName(): string
    {
        $words = explode(' ', $this->name);
        return $words[0];
    }

    /**
     * Get the user's full name.
     *
     * @return string The user's full name
     */
    public function getFullName(): string
    {
        return $this->name;
    }

    /**
     * Check if the user is an admin.
     *
     * @return bool True if the user is an admin
     */
    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    /**
     * Get the user as an array.
     *
     * @return array<string, mixed> The user data as array
     */
    public function toArray(): array
    {
        $arr = [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password,
            'locale' => $this->locale,
            'google_id' => $this->googleId,
            'google_token' => $this->googleToken,
            'google_refresh_token' => $this->googleRefreshToken,
            'facebook_id' => $this->facebookId,
            'facebook_token' => $this->facebookToken,
            'facebook_refresh_token' => $this->facebookRefreshToken,
            'email_verified_at' => $this->emailVerifiedAt?->toISOString(),
            'created_at' => $this->createdAt?->toISOString(),
            'updated_at' => $this->updatedAt?->toISOString(),
            'roles' => $this->roles,
            'permissions' => $this->permissions,
        ];
        if ($this->passwordConfirmation !== null) {
            $arr['password_confirmation'] = $this->passwordConfirmation;
        }
        return $arr;
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
            name: $changes['name'] ?? $this->name,
            email: $changes['email'] ?? $this->email,
            password: $changes['password'] ?? $this->password,
            locale: $changes['locale'] ?? $this->locale,
            googleId: $changes['google_id'] ?? $changes['googleId'] ?? $this->googleId,
            googleToken: $changes['google_token'] ?? $changes['googleToken'] ?? $this->googleToken,
            googleRefreshToken: $changes['google_refresh_token'] ?? $changes['googleRefreshToken'] ?? $this->googleRefreshToken,
            facebookId: $changes['facebook_id'] ?? $changes['facebookId'] ?? $this->facebookId,
            facebookToken: $changes['facebook_token'] ?? $changes['facebookToken'] ?? $this->facebookToken,
            facebookRefreshToken: $changes['facebook_refresh_token'] ?? $changes['facebookRefreshToken'] ?? $this->facebookRefreshToken,
            emailVerifiedAt: $changes['email_verified_at'] ?? $changes['emailVerifiedAt'] ?? $this->emailVerifiedAt,
            createdAt: $changes['created_at'] ?? $changes['createdAt'] ?? $this->createdAt,
            updatedAt: $changes['updated_at'] ?? $changes['updatedAt'] ?? $this->updatedAt,
            roles: $changes['roles'] ?? $this->roles,
            permissions: $changes['permissions'] ?? $this->permissions,
            passwordConfirmation: $changes['password_confirmation'] ?? $this->passwordConfirmation,
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
        $rules = [
            'id' => 'nullable|integer|min:1',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'password' => 'nullable|string|min:8',
            'locale' => 'nullable|string|in:en,fr',
            'google_id' => 'nullable|string|max:255',
            'google_token' => 'nullable|string',
            'google_refresh_token' => 'nullable|string',
            'facebook_id' => 'nullable|string|max:255',
            'facebook_token' => 'nullable|string',
            'facebook_refresh_token' => 'nullable|string',
            'email_verified_at' => 'nullable|date',
            'created_at' => 'nullable|date',
            'updated_at' => 'nullable|date',
            'roles' => 'nullable|array',
            'roles.*' => 'string|in:' . implode(',', \App\Enums\UserRole::values()),
            'permissions' => 'nullable|array',
            'permissions.*' => 'string',
        ];
        
        // Add password confirmation rule if password is provided
        if ($this->password !== null && $this->password !== '' && $this->password !== '0') {
            $rules['password'] = 'required|string|min:8|confirmed';
            $rules['password_confirmation'] = 'required|string|min:8';
        }
        
        // Get custom messages and attributes
        $messages = $validationService->getCustomValidationMessages();
        $attributes = $validationService->getCustomAttributeNames();
        
        // Validate using the service
        $errors = $validationService->validateDTO($this, $rules, $messages, $attributes);
        
        return $errors;
    }


} 