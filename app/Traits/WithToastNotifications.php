<?php

namespace App\Traits;

use Flux\Flux;

/**
 * Trait WithToastNotifications
 *
 * This trait provides methods for displaying toast notifications using Flux UI.
 */
trait WithToastNotifications
{
    /**
     * Display a success toast notification.
     *
     * @param string $message The message to display
     * @param string|null $heading Optional heading for the toast
     * @param int $duration Duration in milliseconds (default: 5000)
     * @return void
     */
    public function showSuccessToast(string $message, ?string $heading = null, int $duration = 5000): void
    {
        Flux::toast(
            text: $message,
            heading: $heading,
            variant: 'success',
            duration: $duration
        );
    }

    /**
     * Display a warning toast notification.
     *
     * @param string $message The message to display
     * @param string|null $heading Optional heading for the toast
     * @param int $duration Duration in milliseconds (default: 5000)
     * @return void
     */
    public function showWarningToast(string $message, ?string $heading = null, int $duration = 5000): void
    {
        Flux::toast(
            text: $message,
            heading: $heading,
            variant: 'warning',
            duration: $duration
        );
    }

    /**
     * Display an error toast notification.
     *
     * @param string $message The message to display
     * @param string|null $heading Optional heading for the toast
     * @param int $duration Duration in milliseconds (default: 5000)
     * @return void
     */
    public function showErrorToast(string $message, ?string $heading = null, int $duration = 5000): void
    {
        Flux::toast(
            text: $message,
            heading: $heading,
            variant: 'danger',
            duration: $duration
        );
    }

    /**
     * Display a toast notification with a translated message.
     *
     * @param string $key The translation key
     * @param array $replace Values to replace in the translation
     * @param string|null $heading Optional heading for the toast
     * @param string $variant Toast variant (success, warning, danger)
     * @param int $duration Duration in milliseconds (default: 5000)
     * @return void
     */
    public function showTranslatedToast(string $key, array $replace = [], ?string $heading = null, string $variant = 'success', int $duration = 5000): void
    {
        Flux::toast(
            text: __($key, $replace),
            heading: $heading ? __($heading) : null,
            variant: $variant,
            duration: $duration
        );
    }
}
