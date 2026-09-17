<?php

namespace App\Repositories;

use App\Models\MasterPrompt;

class MasterPromptRepository extends BaseRepository
{
    public function __construct(MasterPrompt $model)
    {
        parent::__construct($model);
    }
}
