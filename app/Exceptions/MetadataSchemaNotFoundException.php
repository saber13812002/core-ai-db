<?php

namespace App\Exceptions;

use Exception;

/**
 * The referenced metadata schema name/UUID exists (or looks like a direct
 * reference) but no active schema matches it.
 */
class MetadataSchemaNotFoundException extends Exception
{
    public static function for(string $reference): static
    {
        return new static("Metadata schema '{$reference}' was not found or is inactive.");
    }
}
