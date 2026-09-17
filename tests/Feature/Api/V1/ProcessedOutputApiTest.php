<?php

namespace Tests\Feature\Api\V1;

use App\Models\ProcessedOutput;
use Illuminate\Database\Eloquent\Model;

class ProcessedOutputApiTest extends CrudApiTestCase
{
    protected function modelClass(): string
    {
        return ProcessedOutput::class;
    }

    protected function tableName(): string
    {
        return 'processed_outputs';
    }

    protected function collectionUrl(Model $record): string
    {
        return 'api/v1/outputs';
    }

    protected function softDeletes(): bool
    {
        return true;
    }

    protected function updateFieldsFor(Model $record): array
    {
        return ['token_count' => 555];
    }
}
