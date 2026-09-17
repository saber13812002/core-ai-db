<?php

namespace App\Services;

use App\Repositories\VectorCollectionRepository;

class VectorCollectionService extends BaseService
{
    public function __construct(VectorCollectionRepository $repository)
    {
        parent::__construct($repository);
    }
}
