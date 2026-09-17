<?php

namespace Tests\Feature\Api\V1;

use App\Models\ServiceRegistry;
use Illuminate\Database\Eloquent\Model;

class ServiceRegistryApiTest extends CrudApiTestCase
{
    protected function modelClass(): string
    {
        return ServiceRegistry::class;
    }

    protected function tableName(): string
    {
        return 'service_registry';
    }

    protected function collectionUrl(Model $record): string
    {
        return 'api/v1/services';
    }

    protected function updateFieldsFor(Model $record): array
    {
        return ['name' => 'Updated service'];
    }
}
