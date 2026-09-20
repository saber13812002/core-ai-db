<?php

namespace App\Exceptions;

use Exception;

/**
 * A metadata payload was rejected by the resolved MetadataSchema.
 * The individual human-readable problems are carried on $errors.
 */
class MetadataValidationException extends Exception
{
    /** @param array<int, string> $errors */
    public function __construct(public readonly array $errors)
    {
        parent::__construct(implode(' ', $errors));
    }

    /** @param array<int, string> $errors */
    public static function for(array $errors): static
    {
        return new static($errors);
    }
}
