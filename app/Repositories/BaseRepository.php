<?php

namespace App\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

abstract class BaseRepository
{
    public function __construct(protected Model $model) {}

    protected function query(): Builder
    {
        return $this->model->newQuery();
    }

    public function list(int $perPage = 15): LengthAwarePaginator
    {
        $column = $this->orderColumn();

        return $this->query()
            ->when($column !== null, fn (Builder $query) => $query->latest($column))
            ->paginate($perPage);
    }

    /**
     * Column used to order list results (latest first); null to leave ordering to the database.
     */
    protected function orderColumn(): ?string
    {
        return $this->model->getCreatedAtColumn();
    }

    public function find(string|int $id): ?Model
    {
        return $this->query()->whereKey($id)->first();
    }

    public function findOrThrow(string|int $id): Model
    {
        return $this->query()->whereKey($id)->firstOrFail();
    }

    public function create(array $attributes): Model
    {
        $record = $this->query()->create($attributes);

        // Re-hydrate so callers (and the response resources they render)
        // see database defaults — status, queued_at, version_number, … —
        // instead of the null in-memory attributes for omitted fields.
        $record->refresh();

        return $record;
    }

    public function update(string|int $id, array $attributes): Model
    {
        $record = $this->findOrThrow($id);

        $record->update($attributes);

        return $record;
    }

    public function delete(string|int $id): void
    {
        // Soft-deletable models are soft-deleted, all others are hard-deleted.
        $this->findOrThrow($id)->delete();
    }
}
