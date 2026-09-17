<?php

namespace Tests\Feature\Api\V1;

use App\Models\HumanGroundTruth;
use Illuminate\Database\Eloquent\Model;

class HumanGroundTruthApiTest extends CrudApiTestCase
{
    protected function modelClass(): string
    {
        return HumanGroundTruth::class;
    }

    protected function tableName(): string
    {
        return 'human_ground_truth';
    }

    protected function collectionUrl(Model $record): string
    {
        return 'api/v1/ground-truth';
    }

    protected function updateFieldsFor(Model $record): array
    {
        return ['approval_notes' => 'Updated approval notes'];
    }
}
