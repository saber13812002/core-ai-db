<?php

namespace App\Services;

use App\Repositories\ProjectRepository;

class ProjectService extends BaseService
{
    public function __construct(ProjectRepository $repository)
    {
        parent::__construct($repository);
    }
}
