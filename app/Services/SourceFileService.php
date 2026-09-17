<?php

namespace App\Services;

use App\Repositories\SourceFileRepository;

class SourceFileService extends BaseService
{
    public function __construct(SourceFileRepository $repository)
    {
        parent::__construct($repository);
    }
}
