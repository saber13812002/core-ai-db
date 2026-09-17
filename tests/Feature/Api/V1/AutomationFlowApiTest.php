<?php

namespace Tests\Feature\Api\V1;

use App\Models\AutomationFlow;
use Illuminate\Database\Eloquent\Model;

class AutomationFlowApiTest extends CrudApiTestCase
{
    protected function modelClass(): string
    {
        return AutomationFlow::class;
    }

    protected function tableName(): string
    {
        return 'automation_flows';
    }

    protected function collectionUrl(Model $record): string
    {
        return 'api/v1/automation-flows';
    }

    protected function storeOverrides(Model $record): array
    {
        return ['platform_flow_id' => null];
    }

    protected function updateFieldsFor(Model $record): array
    {
        return ['description' => 'Updated automation flow description'];
    }
}
