<?php

namespace Tests\Feature\Api\V1;

use App\Models\AutomationJob;
use Illuminate\Database\Eloquent\Model;

class AutomationJobApiTest extends CrudApiTestCase
{
    protected function modelClass(): string
    {
        return AutomationJob::class;
    }

    protected function tableName(): string
    {
        return 'automation_jobs';
    }

    protected function collectionUrl(Model $record): string
    {
        return 'api/v1/jobs';
    }

    protected function updateFieldsFor(Model $record): array
    {
        return ['priority' => 9];
    }
}
