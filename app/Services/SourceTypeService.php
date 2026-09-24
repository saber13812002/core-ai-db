<?php

namespace App\Services;

use App\Repositories\SourceTypeRepository;

class SourceTypeService extends BaseService
{
    public function __construct(SourceTypeRepository $repository)
    {
        parent::__construct($repository);
    }
}
