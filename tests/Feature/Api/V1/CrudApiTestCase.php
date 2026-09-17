<?php

namespace Tests\Feature\Api\V1;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

abstract class CrudApiTestCase extends TestCase
{
    use RefreshDatabase;

    protected ?Model $seedRecord = null;

    abstract protected function modelClass(): string;

    abstract protected function tableName(): string;

    abstract protected function collectionUrl(Model $record): string;

    protected function itemUrl(Model $record): string
    {
        return $this->collectionUrl($record).'/'.rawurlencode((string) $record->getKey());
    }

    protected function createSeedRecord(): Model
    {
        return $this->seedRecord ??= $this->modelClass()::factory()->create();
    }

    /**
     * Payload overrides applied on top of the seed record's attributes (unique columns, etc.).
     *
     * @return array<string, mixed>
     */
    protected function storeOverrides(Model $record): array
    {
        return [];
    }

    /**
     * @return array<string, mixed>
     */
    protected function updateFieldsFor(Model $record): array
    {
        return [];
    }

    protected function softDeletes(): bool
    {
        return false;
    }

    /**
     * @return array<string, mixed>
     */
    protected function storePayloadFor(Model $record): array
    {
        $attributes = $record->fresh()->attributesToArray();

        $payload = array_diff_key($attributes, array_flip(['id', 'created_at', 'updated_at', 'deleted_at']));

        return array_merge($payload, $this->storeOverrides($record));
    }

    public function test_can_list_records(): void
    {
        $record = $this->createSeedRecord();

        $this->getJson($this->collectionUrl($record))
            ->assertOk()
            ->assertJsonPath('data.0.id', $record->getKey());
    }

    public function test_can_create_record(): void
    {
        $record = $this->createSeedRecord();

        $response = $this->postJson($this->collectionUrl($record), $this->storePayloadFor($record));

        $response->assertCreated();

        $createdId = $response->json('data.id');
        $this->assertNotNull($createdId);
        $this->assertDatabaseHas($this->tableName(), ['id' => $createdId]);
    }

    public function test_can_show_record(): void
    {
        $record = $this->createSeedRecord();

        $this->getJson($this->itemUrl($record))
            ->assertOk()
            ->assertJsonPath('data.id', $record->getKey());
    }

    public function test_can_update_record(): void
    {
        $record = $this->createSeedRecord();
        $changes = $this->updateFieldsFor($record);
        $this->assertNotEmpty($changes);

        $this->patchJson($this->itemUrl($record), $changes)->assertOk();

        $this->assertDatabaseHas($this->tableName(), array_merge(['id' => $record->getKey()], $changes));
    }

    public function test_can_delete_record(): void
    {
        $record = $this->createSeedRecord();

        $this->deleteJson($this->itemUrl($record))->assertNoContent();

        if ($this->softDeletes()) {
            $this->assertSoftDeleted($this->tableName(), ['id' => $record->getKey()]);

            return;
        }

        $this->assertDatabaseMissing($this->tableName(), ['id' => $record->getKey()]);
    }
}
