<?php

namespace App\Exceptions;

use Exception;

/**
 * The stored binary of a source file can no longer be served.
 */
class FileDownloadException extends Exception
{
    public static function notFound(): static
    {
        return new static('The stored file could not be found on disk.', 410);
    }

    public function __construct(string $message, public readonly int $httpStatus = 410)
    {
        parent::__construct($message);
    }
}
