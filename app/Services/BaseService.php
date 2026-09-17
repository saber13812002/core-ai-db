<?php

namespace App\Services;

use App\Repositories\BaseRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;

abstract class BaseService
{
    public function __construct(protected BaseRepository $repository) {}

    public function list(int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->list($perPage);
    }

    public function create(array $attributes): Model
    {
        return $this->repository->create($attributes);
    }

    public function find(string|int $id): ?Model
    {
        return $this->repository->find($id);
    }

    public function findOrThrow(string|int $id): Model
    {
        return $this->repository->findOrThrow($id);
    }

    public function update(string|int $id, array $attributes): Model
    {
        return $this->repository->update($id, $attributes);
    }

    public function delete(string|int $id): void
    {
        $this->repository->delete($id);
    }
}
