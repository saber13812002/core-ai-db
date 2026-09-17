<?php

namespace App\Repositories;

use App\Models\VectorCollection;

class VectorCollectionRepository extends BaseRepository
{
    public function __construct(VectorCollection $model)
    {
        parent::__construct($model);
    }
}
