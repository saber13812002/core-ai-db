<?php

namespace App\Repositories;

use App\Models\SourceFile;

class SourceFileRepository extends BaseRepository
{
    public function __construct(SourceFile $model)
    {
        parent::__construct($model);
    }
}
