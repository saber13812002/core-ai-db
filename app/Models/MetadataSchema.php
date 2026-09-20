<?php

namespace App\Models;

use Database\Factories\MetadataSchemaFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * A declarative definition of allowed metadata keys for source files.
 * The schema body maps key => {type, required, enum?, additional?}.
 *
 * @property string $id
 * @property string $name
 * @property string $scope
 * @property array<string, mixed>|null $schema
 * @property bool $is_active
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
#[Fillable([
    'name', 'scope', 'schema', 'is_active',
])]
#[Table(name: 'metadata_schemas')]
class MetadataSchema extends Model
{
    /** @use HasFactory<MetadataSchemaFactory> */
    use HasFactory, HasUuids;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'schema' => 'array',
            'is_active' => 'boolean',
        ];
    }
}
