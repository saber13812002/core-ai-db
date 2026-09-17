<?php

namespace App\Services;

use App\Repositories\MasterPromptRepository;

class MasterPromptService extends BaseService
{
    public function __construct(MasterPromptRepository $repository)
    {
        parent::__construct($repository);
    }
}
