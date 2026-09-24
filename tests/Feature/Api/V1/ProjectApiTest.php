<?php

namespace Tests\Feature\Api\V1;

use App\Models\Project;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ProjectApiTest extends CrudApiTestCase
{
    protected function modelClass(): string
    {
        return Project::class;
    }

    protected function tableName(): string
    {
        return 'projects';
    }

    protected function collectionUrl(Model $record): string
    {
        return 'api/v1/projects';
    }

    protected function storeOverrides(Model $record): array
    {
        return ['name' => 'proj-'.Str::random(12)];
    }

    protected function updateFieldsFor(Model $record): array
    {
        return ['description' => 'Updated project'];
    }
}
