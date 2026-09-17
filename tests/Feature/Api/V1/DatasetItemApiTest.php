<?php

namespace Tests\Feature\Api\V1;

use App\Models\DatasetItem;
use Illuminate\Database\Eloquent\Model;

class DatasetItemApiTest extends CrudApiTestCase
{
    protected function modelClass(): string
    {
        return DatasetItem::class;
    }

    protected function tableName(): string
    {
        return 'dataset_items';
    }

    protected function collectionUrl(Model $record): string
    {
        return 'api/v1/dataset-items';
    }

    protected function storeOverrides(Model $record): array
    {
        return [
            'input_cleaned_id' => null,
            'ground_truth_id' => null,
        ];
    }

    protected function updateFieldsFor(Model $record): array
    {
        return ['sequence_order' => 77];
    }
}
