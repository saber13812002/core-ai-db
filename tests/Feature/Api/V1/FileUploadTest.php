<?php

namespace Tests\Feature\Api\V1;

use App\Models\MetadataSchema;
use App\Models\SourceFile;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Testing\TestResponse;

class FileUploadTest extends ApiTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');
    }

    private function upload(UploadedFile $file, array $extra = []): TestResponse
    {
        return $this->post('/api/v1/files/upload', array_merge([
            'file' => $file,
        ], $extra));
    }

    // US-01..US-05: happy path per file type

    public function test_word_document_upload_registers_source_file(): void
    {
        $file = UploadedFile::fake()->create('sample.docx', 12, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document');

        $response = $this->upload($file, ['metadata' => ['speaker' => 'Dr. Ali']]);

        $response->assertCreated()
            ->assertJsonPath('data.file_type', 'docx')
            ->assertJsonPath('data.processing_status', 'registered')
            ->assertJsonPath('data.original_filename', 'sample.docx')
            ->assertJsonPath('data.file_size_bytes', 12 * 1024)
            ->assertJsonPath('data.metadata.speaker', 'Dr. Ali')
            ->assertJsonPath('data.created_by', $this->apiKey->id)
            ->assertJsonPath('duplicate', false);

        $id = $response->json('data.id');

        $this->assertNotNull($id);
        $this->assertNotEmpty($response->json('data.checksum_sha256'));
        $this->assertNotEmpty($response->json('data.storage_path'));

        $this->assertDatabaseHas('source_files', [
            'id' => $id,
            'file_type' => 'docx',
            'processing_status' => 'registered',
            'created_by' => $this->apiKey->id,
        ]);

        $this->assertFileExists(Storage::disk('local')->path($response->json('data.storage_path')));
    }

    public function test_pdf_txt_audio_and_video_upload_all_register(): void
    {
        $cases = [
            ['sample.pdf', 'application/pdf'],
            ['notes.txt', 'text/plain'],
            ['lecture.mp3', 'audio/mpeg'],
            ['lesson.mp4', 'video/mp4'],
        ];

        foreach ($cases as $index => [$name, $mime]) {
            // Unique content so the checksum dedupe logic is not triggered;
            // explicit mime so it does not depend on the mime database lookup.
            $response = $this->upload(
                UploadedFile::fake()->createWithContent($name, "distinct-content-{$index}-{$name}")->mimeType($mime)
            );

            $response->assertCreated()
                ->assertJsonPath('data.file_type', pathinfo($name, PATHINFO_EXTENSION))
                ->assertJsonPath('data.processing_status', 'registered');
        }
    }

    public function test_multipart_metadata_fields_are_collected(): void
    {
        $file = UploadedFile::fake()->create('course.mp3', 10, 'audio/mpeg');

        $response = $this->post('/api/v1/files/upload', [
            'file' => $file,
            'external_ref' => 'upload-001',
            'language' => 'en',
            'metadata' => ['speaker' => 'Dr. Reza', 'topic' => 'Databases'],
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.external_ref', 'upload-001')
            ->assertJsonPath('data.language', 'en')
            ->assertJsonPath('data.metadata.speaker', 'Dr. Reza')
            ->assertJsonPath('data.metadata.topic', 'Databases');
    }

    // Duplicate handling (plan §8: duplicate checksum 200 + duplicate flag)

    public function test_uploading_identical_content_returns_existing_file_as_duplicate(): void
    {
        $content = 'the same lecture bytes';
        $first = $this->upload(UploadedFile::fake()->createWithContent('one.mp3', $content));
        $first->assertCreated()->assertJsonPath('duplicate', false);

        $second = $this->upload(UploadedFile::fake()->createWithContent('two.mp3', $content));
        $second->assertOk()
            ->assertJsonPath('duplicate', true)
            ->assertJsonPath('data.id', $first->json('data.id'))
            ->assertJsonPath('data.original_filename', 'one.mp3');

        $this->assertSame(1, SourceFile::where('checksum_sha256', $first->json('data.checksum_sha256'))->count());
    }

    // Download round-trip (US-01..05 visible state)

    public function test_uploaded_file_can_be_downloaded_with_original_name_and_mime(): void
    {
        $content = str_repeat('lecture bytes | ', 64);
        $upload = $this->upload(UploadedFile::fake()->createWithContent('lecture.mp3', $content));
        $upload->assertCreated();
        $id = $upload->json('data.id');

        $download = $this->get('/api/v1/files/'.$id.'/download');

        $download->assertOk()
            ->assertHeader('Content-Disposition', 'attachment; filename=lecture.mp3')
            ->assertStreamedContent($content);
    }

    public function test_download_returns_410_when_binary_is_missing_from_disk(): void
    {
        $file = SourceFile::factory()->create(['storage_path' => 'uploads/gone.mp3']);

        $this->get('/api/v1/files/'.$file->id.'/download')
            ->assertStatus(410)
            ->assertJsonPath('error.code', 'file_unavailable');
    }

    // Negative matrix (plan §8, File rows)

    public function test_upload_without_file_is_422(): void
    {
        $this->post('/api/v1/files/upload')
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['file']);
    }

    public function test_empty_file_is_422(): void
    {
        $this->upload(UploadedFile::fake()->create('empty.mp3', 0, 'audio/mpeg'))
            ->assertUnprocessable()
            ->assertJsonPath('error.code', 'file_empty');
    }

    public function test_oversized_file_is_413(): void
    {
        $maxKb = (int) (config('ai-factory.upload.max_bytes') / 1024);

        $this->post('/api/v1/files/upload', [
            'file' => UploadedFile::fake()->create('huge.mp3', $maxKb + 1, 'audio/mpeg'),
        ])->assertStatus(413)
            ->assertJsonPath('error.code', 'file_too_large');
    }

    public function test_unknown_extension_is_415(): void
    {
        $this->upload(UploadedFile::fake()->create('malware.exe', 4, 'application/x-msdownload'))
            ->assertStatus(415)
            ->assertJsonPath('error.code', 'unsupported_file_type');
    }

    public function test_mime_extension_mismatch_is_415(): void
    {
        $this->upload(UploadedFile::fake()->create('actually-exe.mp3', 4, 'application/x-msdownload'))
            ->assertStatus(415)
            ->assertJsonPath('error.code', 'mime_mismatch');
    }

    public function test_weird_filename_is_sanitized_before_storage(): void
    {
        $upload = $this->upload(UploadedFile::fake()->create('..\..\evil<script>.mp3', 4, 'audio/mpeg'));

        $upload->assertCreated()
            ->assertJsonPath('data.original_filename', 'evil<script>.mp3');

        $stored = Storage::disk('local')->path($upload->json('data.storage_path'));
        $this->assertStringContainsString('uploads/', $stored);
        $this->assertStringNotContainsString('..', dirname($stored));
    }

    public function test_duplicate_external_ref_is_422(): void
    {
        SourceFile::factory()->create(['external_ref' => 'taken-001']);

        $this->upload(UploadedFile::fake()->create('a.mp3', 4, 'audio/mpeg'), ['external_ref' => 'taken-001'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['external_ref']);
    }

    // Metadata schema on upload (US-08)

    public function test_upload_metadata_is_validated_against_file_type_scope_schema(): void
    {
        MetadataSchema::factory()->create([
            'scope' => 'mp3',
            'schema' => [
                'speaker' => ['type' => 'string', 'required' => true],
            ],
        ]);

        // Valid payload passes.
        $this->upload(UploadedFile::fake()->create('ok.mp3', 4, 'audio/mpeg'), ['metadata' => ['speaker' => 'Ali']])
            ->assertCreated();

        // Missing required key fails.
        $this->upload(UploadedFile::fake()->create('no-speaker.mp3', 4, 'audio/mpeg'), ['metadata' => ['language' => 'fa']])
            ->assertUnprocessable()
            ->assertJsonPath('error.code', 'metadata_invalid')
            ->assertJsonFragment(['metadata.speaker is required by the schema.']);
    }

    public function test_upload_metadata_type_mismatch_is_422(): void
    {
        MetadataSchema::factory()->create([
            'scope' => 'mp3',
            'schema' => [
                'speaker' => ['type' => 'string', 'required' => true],
            ],
        ]);

        $this->upload(UploadedFile::fake()->create('bad-type.mp3', 4, 'audio/mpeg'), ['metadata' => ['speaker' => 123]])
            ->assertUnprocessable()
            ->assertJsonPath('error.code', 'metadata_invalid')
            ->assertJsonFragment(['metadata.speaker must be a string.']);
    }

    public function test_upload_metadata_unknown_field_is_422(): void
    {
        MetadataSchema::factory()->create([
            'scope' => 'mp3',
            'schema' => [
                'speaker' => ['type' => 'string', 'required' => true],
            ],
        ]);

        $this->upload(UploadedFile::fake()->create('extra.mp3', 4, 'audio/mpeg'), ['metadata' => ['speaker' => 'Ali', 'hacker' => 'x']])
            ->assertUnprocessable()
            ->assertJsonFragment(['metadata.hacker is not allowed by the schema.']);
    }

    public function test_upload_explicit_metadata_schema_by_name_is_enforced(): void
    {
        MetadataSchema::factory()->create([
            'name' => 'course-files',
            'scope' => 'global',
            'schema' => [
                'course' => ['type' => 'string', 'required' => true, 'enum' => ['db', 'web']],
            ],
        ]);

        $this->upload(
            UploadedFile::fake()->create('named.txt', 4, 'text/plain'),
            ['metadata_schema' => 'course-files', 'metadata' => ['course' => 'ai']],
        )
            ->assertUnprocessable()
            ->assertJsonFragment(['metadata.course must be one of: db, web.']);

        $this->upload(
            UploadedFile::fake()->create('named-ok.txt', 4, 'text/plain'),
            ['metadata_schema' => 'course-files', 'metadata' => ['course' => 'db']],
        )
            ->assertCreated();
    }

    public function test_upload_with_unknown_explicit_metadata_schema_is_422(): void
    {
        $this->upload(
            UploadedFile::fake()->create('x.txt', 4, 'text/plain'),
            ['metadata_schema' => 'does-not-exist', 'metadata' => ['a' => 'b']],
        )
            ->assertUnprocessable()
            ->assertJsonPath('error.code', 'metadata_schema_not_found');
    }
}
