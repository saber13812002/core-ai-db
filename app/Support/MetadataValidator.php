<?php

namespace App\Support;

/**
 * Validates a metadata array against a MetadataSchema definition body.
 * The schema body maps key => {type: string|integer|boolean|object,
 * required?: bool, enum?: array, additional?: allow|reject (for object
 * values only)}. Unknown keys are always rejected.
 */
class MetadataValidator
{
    /**
     * @param  array<string, mixed>  $metadata
     * @param  array<string, array<string, mixed>>  $schema
     * @return array<int, string> List of human-readable validation errors (empty when valid).
     */
    public static function validate(array $metadata, array $schema): array
    {
        $errors = [];

        foreach ($schema as $key => $definition) {
            $type = $definition['type'] ?? 'string';
            $required = (bool) ($definition['required'] ?? false);
            $enum = $definition['enum'] ?? null;
            $additional = $definition['additional'] ?? 'allow';

            if (! array_key_exists($key, $metadata)) {
                if ($required) {
                    $errors[] = "metadata.{$key} is required by the schema.";
                }

                continue;
            }

            $value = $metadata[$key];

            $errors = array_merge($errors, self::validateValue("metadata.{$key}", $value, $type, $enum, $additional));
        }

        foreach (array_keys($metadata) as $key) {
            if (! array_key_exists($key, $schema)) {
                $errors[] = "metadata.{$key} is not allowed by the schema.";
            }
        }

        return $errors;
    }

    /**
     * @param  array<int, mixed>|null  $enum
     * @return array<int, string>
     */
    private static function validateValue(string $field, mixed $value, string $type, ?array $enum, string $additional): array
    {
        $errors = [];

        switch ($type) {
            case 'string':
                if (! is_string($value)) {
                    $errors[] = "{$field} must be a string.";
                }

                break;
            case 'integer':
                if (! is_int($value) && ! (is_string($value) && preg_match('/^-?\d+$/', $value))) {
                    $errors[] = "{$field} must be an integer.";
                }

                break;
            case 'boolean':
                if (! is_bool($value)) {
                    $errors[] = "{$field} must be a boolean.";
                }

                break;
            case 'object':
                if (! is_array($value)) {
                    $errors[] = "{$field} must be an object.";
                } elseif ($additional === 'reject') {
                    foreach ($value as $subValue) {
                        if (! is_scalar($subValue)) {
                            $errors[] = "{$field} may only contain scalar values.";

                            break;
                        }
                    }
                }

                break;
        }

        if ($errors === [] && $enum !== null) {
            $candidate = is_string($value) && preg_match('/^-?\d+$/', $value) ? (int) $value : $value;

            if (! in_array($candidate, $enum, true)) {
                $errors[] = "{$field} must be one of: ".implode(', ', array_map(strval(...), $enum)).'.';
            }
        }

        return $errors;
    }
}
