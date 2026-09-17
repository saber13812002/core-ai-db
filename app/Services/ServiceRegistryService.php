<?php

namespace App\Services;

use App\Repositories\ServiceRegistryRepository;

class ServiceRegistryService extends BaseService
{
    public function __construct(ServiceRegistryRepository $repository)
    {
        parent::__construct($repository);
    }
}
