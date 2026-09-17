<?php

namespace Tests\Feature\Api\V1;

use App\Models\AutomationAction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class AutomationActionApiTest extends CrudApiTestCase
{
    protected function modelClass(): string
    {
        return AutomationAction::class;
    }

    protected function tableName(): string
    {
        return 'automation_actions';
    }

    protected function collectionUrl(Model $record): string
    {
        return 'api/v1/automation-actions';
    }

    protected function storeOverrides(Model $record): array
    {
        return ['code' => 'aa-'.Str::random(12)];
    }

    protected function updateFieldsFor(Model $record): array
    {
        return ['name_fa' => 'Updated automation action'];
    }
}
