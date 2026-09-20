<?php

namespace Tests\Feature\Api\V1;

use App\Models\MetadataSchema;
use App\Models\SourceFile;

class MetadataFilesApiTest extends ApiTestCase
{
    // US-07: metadata query filter

    public function test_files_can_be_filtered_by_metadata_field(): void
    {
        $match = SourceFile::factory()->create([
            'file_type' => 'mp3',
            'language' => 'fa',
            'metadata' => ['speaker' => 'Dr. Ali', 'topic' => 'Databases'],
        ]);
        SourceFile::factory()->create(['metadata' => ['speaker' => 'Someone Else']]);

        $this->getJson('api/v1/files?metadata[speaker]=Dr. Ali')
            ->assertOk()
            ->assertJsonPath('data.0.id', $match->id)
            ->assertJsonCount(1, 'data');

        $this->getJson('api/v1/files?metadata[topic]=Databases')
            ->assertJsonCount(1, 'data');

        $this->getJson('api/v1/files?metadata[topic]=Networking')
            ->assertJsonCount(0, 'data');

        // Column value (language) is not part of metadata and must not match.
        $this->getJson('api/v1/files?metadata[language]=fa')
            ->assertJsonCount(0, 'data');
    }

    public function test_multiple_metadata_filters_are_combined(): void
    {
        SourceFile::factory()->create(['metadata' => ['speaker' => 'Ali', 'topic' => 'Databases']]);
        SourceFile::factory()->create(['metadata' => ['speaker' => 'Ali', 'topic' => 'Web']]);

        $this->getJson('api/v1/files?metadata[speaker]=Ali&metadata[topic]=Databases')
            ->assertJsonCount(1, 'data');
    }

    // US-08: schema validation on JSON store/update (no binary)

    public function test_store_file_with_metadata_violating_global_schema_is_422(): void
    {
        MetadataSchema::factory()->create([
            'scope' => 'global',
            'schema' => [
                'speaker' => ['type' => 'string', 'required' => true],
            ],
        ]);

        $payload = [
            'file_type' => 'pdf',
            'storage_path' => 'files/missing.pdf',
            'metadata' => ['no_speaker_here' => 'true'],
        ];

        $this->postJson('api/v1/files', $payload)
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['metadata'])
            ->assertJsonFragment(['metadata.speaker is required by the schema.']);

        $this->assertDatabaseCount('source_files', 0);
    }

    public function test_store_file_metadata_type_mismatch_is_422(): void
    {
        MetadataSchema::factory()->create([
            'scope' => 'pdf',
            'schema' => [
                'speaker' => ['type' => 'string', 'required' => true],
            ],
        ]);

        $this->postJson('api/v1/files', [
            'file_type' => 'pdf',
            'storage_path' => 'files/missing.pdf',
            'metadata' => ['speaker' => 123],
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['metadata'])
            ->assertJsonFragment(['metadata.speaker must be a string.']);
    }

    public function test_store_file_with_explicit_metadata_schema_id_is_enforced(): void
    {
        $schema = MetadataSchema::factory()->create([
            'scope' => 'pdf',
            'schema' => [
                'instructor' => ['type' => 'string', 'required' => true],
            ],
        ]);

        $this->postJson('api/v1/files', [
            'file_type' => 'pdf',
            'storage_path' => 'files/missing.pdf',
            'metadata_schema_id' => $schema->id,
            'metadata' => ['other' => 'value'],
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['metadata'])
            ->assertJsonFragment(['metadata.instructor is required by the schema.']);

        $this->postJson('api/v1/files', [
            'file_type' => 'pdf',
            'storage_path' => 'files/missing.pdf',
            'metadata_schema_id' => $schema->id,
            'metadata' => ['instructor' => 'Dr. Ali'],
        ])->assertCreated()
            ->assertJsonPath('data.metadata_schema_id', $schema->id);
    }

    public function test_update_file_metadata_against_schema_is_validated(): void
    {
        MetadataSchema::factory()->create([
            'scope' => 'global',
            'schema' => [
                'speaker' => ['type' => 'string', 'required' => true],
            ],
        ]);

        $file = SourceFile::factory()->create(['metadata' => ['speaker' => 'Ali']]);

        $this->patchJson("api/v1/files/{$file->id}", ['metadata' => ['speaker' => 99]])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['metadata']);

        $this->patchJson("api/v1/files/{$file->id}", ['metadata' => ['speaker' => 'Reza']])
            ->assertOk()
            ->assertJsonPath('data.metadata.speaker', 'Reza');
    }
}
