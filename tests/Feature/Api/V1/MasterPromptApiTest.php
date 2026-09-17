<?php

namespace Tests\Feature\Api\V1;

use App\Models\MasterPrompt;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class MasterPromptApiTest extends CrudApiTestCase
{
    protected function modelClass(): string
    {
        return MasterPrompt::class;
    }

    protected function tableName(): string
    {
        return 'prompts';
    }

    protected function collectionUrl(Model $record): string
    {
        return 'api/v1/prompts';
    }

    protected function softDeletes(): bool
    {
        return true;
    }

    protected function storeOverrides(Model $record): array
    {
        return [
            'family_id' => (string) Str::uuid(),
            'version' => '2.0',
        ];
    }

    protected function updateFieldsFor(Model $record): array
    {
        return ['purpose' => 'Updated prompt purpose'];
    }
}
