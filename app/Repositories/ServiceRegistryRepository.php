<?php

namespace App\Repositories;

use App\Models\ServiceRegistry;

class ServiceRegistryRepository extends BaseRepository
{
    public function __construct(ServiceRegistry $model)
    {
        parent::__construct($model);
    }
}
