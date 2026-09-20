<?php

namespace App\Exceptions;

use Exception;

/**
 * A rejected upload. Carries the HTTP status (413 too large, 415 unsupported)
 * and a machine-readable error code surfaced as {error: {code, message}}.
 */
class FileUploadException extends Exception
{
    public function __construct(
        string $message,
        public readonly string $errorCode,
        public readonly int $httpStatus = 422,
    ) {
        parent::__construct($message);
    }

    public static function emptyFile(): static
    {
        return new static(
            'The uploaded file is empty.',
            'file_empty',
            422,
        );
    }

    public static function tooLarge(): static
    {
        return new static(
            'The file exceeds the maximum allowed size.',
            'file_too_large',
            413,
        );
    }

    public static function unsupportedExtension(): static
    {
        return new static(
            'The file type is not accepted. Allowed types: '.implode(', ', array_keys(config('ai-factory.upload.allowed'))).'.',
            'unsupported_file_type',
            415,
        );
    }

    public static function mimeMismatch(string $declared, string $expected): static
    {
        return new static(
            "The declared MIME type ({$declared}) does not match the file extension. Expected: {$expected}.",
            'mime_mismatch',
            415,
        );
    }
}
