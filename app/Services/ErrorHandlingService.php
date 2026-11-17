<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;

/**
 * ErrorHandlingService
 *
 * Centralized error handling to prevent sensitive information exposure
 * while maintaining good UX and comprehensive logging.
 *
 * Usage:
 * ```
 * try {
 *     // risky operation
 * } catch (\Exception $e) {
 *     $message = ErrorHandlingService::getUserFriendlyMessage($e);
 *     $this->dispatch('toast', variant: 'error', message: $message);
 * }
 * ```
 */
class ErrorHandlingService
{
    /**
     * Get user-friendly error message without exposing sensitive details
     *
     * @param \Throwable $exception
     * @param string $context Optional context for better error messages
     * @return string User-safe error message
     */
    public static function getUserFriendlyMessage(\Throwable $exception, string $context = 'operasi'): string
    {
        // Log full exception for debugging (with stack trace)
        Log::error("Error during {$context}", [
            'exception' => get_class($exception),
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString(),
        ]);

        // Return user-friendly message based on exception type
        return match (true) {
            // Validation errors - pass through (safe to show)
            $exception instanceof ValidationException => $exception->getMessage(),

            // Database constraint violations
            $exception instanceof QueryException && str_contains($exception->getMessage(), 'Duplicate entry') =>
                "Data sudah ada dalam sistem. Silakan gunakan data yang berbeda.",

            $exception instanceof QueryException && str_contains($exception->getMessage(), 'foreign key constraint') =>
                "Tidak dapat menghapus data karena masih terkait dengan data lain.",

            $exception instanceof QueryException && str_contains($exception->getMessage(), 'Data too long') =>
                "Data yang dimasukkan terlalu panjang. Silakan persingkat.",

            // Custom application exceptions (assumed safe)
            $exception instanceof \App\Exceptions\RolloverException ||
            $exception instanceof \App\Exceptions\InvalidStatusTransitionException ||
            $exception instanceof \App\Exceptions\GroupCapacityExceededException =>
                $exception->getMessage(),

            // Generic database error (hide details)
            $exception instanceof QueryException =>
                "Terjadi kesalahan database. Silakan coba lagi atau hubungi administrator.",

            // File upload errors
            $exception instanceof \Symfony\Component\HttpFoundation\File\Exception\FileException =>
                "Gagal mengunggah file. Silakan coba lagi.",

            // Authentication/Authorization (generic)
            $exception instanceof \Illuminate\Auth\AuthenticationException ||
            $exception instanceof \Illuminate\Auth\Access\AuthorizationException =>
                "Anda tidak memiliki akses untuk melakukan tindakan ini.",

            // Default: Generic message (never expose internal details)
            default => config('app.debug')
                ? $exception->getMessage() // Show in development
                : "Terjadi kesalahan saat {$context}. Silakan coba lagi atau hubungi administrator."
        };
    }

    /**
     * Handle exception in Livewire component context
     *
     * Logs error and dispatches toast notification
     *
     * @param \Livewire\Component $component
     * @param \Throwable $exception
     * @param string $context
     * @return void
     */
    public static function handleInComponent($component, \Throwable $exception, string $context = 'operasi'): void
    {
        $message = self::getUserFriendlyMessage($exception, $context);
        $component->dispatch('toast', variant: 'error', message: $message);
    }

    /**
     * Log and return safe message for API responses
     *
     * @param \Throwable $exception
     * @param string $context
     * @return array ['message' => string, 'code' => int]
     */
    public static function getApiErrorResponse(\Throwable $exception, string $context = 'request'): array
    {
        $message = self::getUserFriendlyMessage($exception, $context);

        $code = match (true) {
            $exception instanceof ValidationException => 422,
            $exception instanceof \Illuminate\Auth\AuthenticationException => 401,
            $exception instanceof \Illuminate\Auth\Access\AuthorizationException => 403,
            $exception instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException => 404,
            default => 500,
        };

        return [
            'message' => $message,
            'code' => $code,
        ];
    }
}
