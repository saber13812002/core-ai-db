<?php

namespace App\Services;

use App\Repositories\MetadataSchemaRepository;

class MetadataSchemaService extends BaseService
{
    public function __construct(MetadataSchemaRepository $repository)
    {
        parent::__construct($repository);
    }
}
