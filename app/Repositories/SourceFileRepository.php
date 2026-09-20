<?php

namespace App\Repositories;

use App\Models\SourceFile;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class SourceFileRepository extends BaseRepository
{
    public function __construct(SourceFile $model)
    {
        parent::__construct($model);
    }

    public function findByChecksum(?string $sha256): ?SourceFile
    {
        if ($sha256 === null || $sha256 === '') {
            return null;
        }

        return $this->query()->where('checksum_sha256', $sha256)->first();
    }

    /**
     * Paginated list, optionally filtered by JSONB metadata field equality:
     * GET /files?metadata[key]=value (repeatable per key).
     *
     * @param  array<string, mixed>|null  $metadataFilters
     */
    public function listWithMetadataFilters(?array $metadataFilters, int $perPage = 15): LengthAwarePaginator
    {
        return $this->query()
            ->when($metadataFilters !== null && $metadataFilters !== [], function (Builder $query) use ($metadataFilters): void {
                $driver = $this->model->getConnection()->getDriverName();

                foreach ($metadataFilters as $key => $value) {
                    // Key is validated first, so interpolation is injection-safe.
                    if (! is_string($key) || ! is_scalar($value) || preg_match('/^[A-Za-z0-9_]+$/', $key) !== 1) {
                        continue;
                    }

                    $value = (string) $value;

                    match ($driver) {
                        'sqlite' => $query->whereRaw('json_extract(metadata, ?) = ?', ['$.'.$key, $value]),
                        'mysql', 'mariadb' => $query->whereRaw('metadata->>? = ?', ['$.'.$key, $value]),
                        default => $query->whereRaw('metadata->>? = ?', ['.'.$key, $value]),
                    };
                }
            })
            ->latest('created_at')
            ->paginate($perPage);
    }
}
