<?php

namespace App\Models;

use Database\Factories\SourceTypeFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\WithoutTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * Catalog of raw source file kinds (audio, video, pdf, docx, ...).
 * Adding a new kind is an insert, not a schema change.
 *
 * @property int $id
 * @property string $code
 * @property string|null $label_fa
 * @property string|null $description
 * @property bool $is_active
 * @property Carbon $created_at
 */
#[Fillable(['code', 'label_fa', 'description', 'is_active'])]
#[Table(name: 'source_types', timestamps: false)]
#[WithoutTimestamps]
class SourceType extends Model
{
    /** @use HasFactory<SourceTypeFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'created_at' => 'datetime',
        ];
    }

    /**
     * @return HasMany<SourceFile, $this>
     */
    public function sourceFiles(): HasMany
    {
        return $this->hasMany(SourceFile::class);
    }
}
