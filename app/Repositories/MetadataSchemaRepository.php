<?php

namespace App\Repositories;

use App\Models\MetadataSchema;

class MetadataSchemaRepository extends BaseRepository
{
    public function __construct(MetadataSchema $model)
    {
        parent::__construct($model);
    }
}
