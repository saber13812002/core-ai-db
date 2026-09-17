<?php

namespace App\Models;

use Database\Factories\ReleaseReportFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\WithoutTimestamps;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * A generated report attached to a model release.
 *
 * @property string $id
 * @property string|null $release_id
 * @property string|null $report_type
 * @property array<string, mixed> $content
 * @property string|null $storage_path
 * @property Carbon $generated_at
 */
#[Fillable([
    'release_id', 'report_type', 'content', 'storage_path', 'generated_at',
])]
#[Table(name: 'release_reports', timestamps: false)]
#[WithoutTimestamps]
class ReleaseReport extends Model
{
    /** @use HasFactory<ReleaseReportFactory> */
    use HasFactory, HasUuids;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'content' => 'array',
            'generated_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<ModelRelease, $this>
     */
    public function release(): BelongsTo
    {
        return $this->belongsTo(ModelRelease::class, 'release_id');
    }
}
