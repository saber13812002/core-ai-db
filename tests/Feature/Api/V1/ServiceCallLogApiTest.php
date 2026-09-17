<?php

namespace Tests\Feature\Api\V1;

use App\Models\ServiceCallLog;
use Illuminate\Database\Eloquent\Model;

class ServiceCallLogApiTest extends CrudApiTestCase
{
    protected function modelClass(): string
    {
        return ServiceCallLog::class;
    }

    protected function tableName(): string
    {
        return 'service_call_logs';
    }

    protected function collectionUrl(Model $record): string
    {
        return 'api/v1/service-call-logs';
    }

    protected function storeOverrides(Model $record): array
    {
        return ['error_message' => null];
    }

    protected function updateFieldsFor(Model $record): array
    {
        return ['http_status' => 201];
    }
}
