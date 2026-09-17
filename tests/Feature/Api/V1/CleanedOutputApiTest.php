<?php

namespace Tests\Feature\Api\V1;

use App\Models\CleanedOutput;
use Illuminate\Database\Eloquent\Model;

class CleanedOutputApiTest extends CrudApiTestCase
{
    protected function modelClass(): string
    {
        return CleanedOutput::class;
    }

    protected function tableName(): string
    {
        return 'cleaned_outputs';
    }

    protected function collectionUrl(Model $record): string
    {
        return 'api/v1/cleaned-outputs';
    }

    protected function softDeletes(): bool
    {
        return true;
    }

    protected function updateFieldsFor(Model $record): array
    {
        return ['quality_score' => 88.5];
    }
}
