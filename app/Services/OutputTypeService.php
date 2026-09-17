<?php

namespace App\Services;

use App\Repositories\OutputTypeRepository;

class OutputTypeService extends BaseService
{
    public function __construct(OutputTypeRepository $repository)
    {
        parent::__construct($repository);
    }
}
