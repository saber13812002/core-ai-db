<?php

namespace App\Http\Requests\Api\V1;

use App\Http\Requests\Api\V1\Concerns\MakesRulesOptional;

class UpdateModelReleaseRequest extends StoreModelReleaseRequest
{
    use MakesRulesOptional;
}
