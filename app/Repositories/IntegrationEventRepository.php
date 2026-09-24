<?php

namespace App\Repositories;

use App\Models\IntegrationEvent;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class IntegrationEventRepository extends BaseRepository
{
    public function __construct(IntegrationEvent $model)
    {
        parent::__construct($model);
    }

    /**
     * Paginated list filtered by reference_type / reference_id / event_type.
     */
    public function listWithFilters(?string $referenceType = null, ?string $referenceId = null, ?string $eventType = null, int $perPage = 15): LengthAwarePaginator
    {
        return $this->query()
            ->when($referenceType !== null && $referenceType !== '', fn (Builder $query) => $query->where('reference_type', $referenceType))
            ->when($referenceId !== null && $referenceId !== '', fn (Builder $query) => $query->where('reference_id', $referenceId))
            ->when($eventType !== null && $eventType !== '', fn (Builder $query) => $query->where('event_type', $eventType))
            ->latest('received_at')
            ->paginate($perPage);
    }

    /**
     * @return array<int, IntegrationEvent>
     */
    public function forReference(string $referenceType, string $referenceId): array
    {
        return $this->query()
            ->where('reference_type', $referenceType)
            ->where('reference_id', $referenceId)
            ->latest('received_at')
            ->limit(100)
            ->get()
            ->all();
    }
}
