# Core AI Factory — E2E Test Coverage & Acceptance Handbook (0 → 100)

> A complete, test-driven acceptance map of everything currently shipped. Every feature in the system is covered by at least one automated feature test; every scenario below is **traceable 1:1 to a test method** (the "Test anchor" column), so an acceptance tester can execute the exact scenario — black-box, layer by layer — and verify the visible state.
>
> - **Scope**: exactly what the test suite in [`tests/Feature`](../tests/Feature) proves today — **157 tests, 441 assertions**, all green.
> - **Out of scope** (planned, not yet implemented — see [`acceptance-delivery-plan.md`](acceptance-delivery-plan.md) phases 3–8): job processing/queue, `jobs/{id}/status|history|result|cancel`, dataset generate/export, training run, benchmark run, lineage, `prompts/resolve`, and the full `PipelineE2eTest`.

## 1. How to run

```bash
php artisan test --compact                                   # full suite (SQLite :memory:, ~7 s)
php artisan test --compact --filter=FileUploadTest            # one feature
php artisan test --compact --filter=test_oversized_file_is_413 # one scenario
php artisan test --compact --filter=ApiAuthenticationTest      # one layer
```

Environment: [`phpunit.xml`](../phpunit.xml) pins `sqlite::memory:` + `RefreshDatabase` (schema is migrated per test from the real migration files in [`database/migrations`](../database/migrations), so migrations and column names are exercised for real). Upload tests additionally fake the `local` storage disk (`Storage::fake('local')`) so no bytes touch real disk.

### Test harness (Layer 0)

| Piece | File | Job |
|---|---|---|
| Base case | [`tests/Feature/Api/V1/ApiTestCase.php`](../tests/Feature/Api/V1/ApiTestCase.php) | `RefreshDatabase` + API key on every request |
| Key provisioning | [`tests/Concerns/InteractsWithApiKeys.php`](../tests/Concerns/InteractsWithApiKeys.php) | creates a real `api_keys` row per test (`key_hash` = SHA-256) and sets `X-API-Key` in `$defaultHeaders`, so the [`EnsureApiKey`](../app/Http/Middleware/EnsureApiKey.php) middleware runs for real on every request |
| CRUD protocol | [`tests/Feature/Api/V1/CrudApiTestCase.php`](../tests/Feature/Api/V1/CrudApiTestCase.php) | 5 generic tests, configured by 5 hooks per resource (see Layer 3) |
| Health probe | [`tests/Feature/ExampleTest.php`](../tests/Feature/ExampleTest.php) | `GET /` → 200 (APP-01) |
| Storage | `Storage::fake('local')` in [`FileUploadTest`](../tests/Feature/Api/V1/FileUploadTest.php) | upload/download write/read fake disk, then assert the bytes |

## 2. Test suite map

| # | Test class | Tests | Covers |
|---|---|---|---|
| 1 | [`ApiAuthenticationTest`](../tests/Feature/Api/V1/ApiAuthenticationTest.php) | 6 | Layer 1 — API-key authentication (AUTH-01…06) |
| 2 | [`FileUploadTest`](../tests/Feature/Api/V1/FileUploadTest.php) | 18 | Layer 3a — upload / download / dedupe / filename / schema-on-upload (UPLOAD-01…18) |
| 3 | [`MetadataFilesApiTest`](../tests/Feature/Api/V1/MetadataFilesApiTest.php) | 6 | Layer 3b — metadata filter + schema validation (META-01…06) |
| 4–28 | 24 resource tests, e.g. [`SourceFileApiTest`](../tests/Feature/Api/V1/SourceFileApiTest.php), [`AiModelApiTest`](../tests/Feature/Api/V1/AiModelApiTest.php), [`VectorCollectionItemApiTest`](../tests/Feature/Api/V1/VectorCollectionItemApiTest.php) | 24 × 5 = 120 | Layer 2 — CRUD contract on every entity (CRUD-01…05 per resource) |
| 29 | [`MetadataSchemaApiTest`](../tests/Feature/Api/V1/MetadataSchemaApiTest.php) | 5 | Layer 2 — CRUD on `metadata_schemas` |
| 30 | [`ExampleTest`](../tests/Feature/ExampleTest.php) | 1 | Layer 0 — APP-01 |

Total: **157**. Every one of the 27 API resources (24 domain + `metadata_schemas`) appears in Layer 2; endpoints unique to `files` (`upload`, `download`) and metadata behavior are covered in Layers 3a/3b.

## 3. Layer 1 — Authentication (applies to every endpoint)

Contract: [`EnsureApiKey`](../app/Http/Middleware/EnsureApiKey.php) on all `api/v1/*` routes (wired in [`bootstrap/app.php`](../bootstrap/app.php)); keys managed via `php artisan api-key:create|list|revoke` ([`app/Console/Commands`](../app/Console/Commands)). Stored keys are SHA-256 hashes in `api_keys` ([`app/Models/ApiKey.php`](../app/Models/ApiKey.php)).

| ID | Scenario | Request | Expected | State check | Test anchor |
|---|---|---|---|---|---|
| AUTH-01 | No API key | `GET /api/v1/files` (no headers) | **401** `{ "error": { "code": "unauthenticated" } }` | — | [`test_request_without_api_key_is_rejected_with_401`](../tests/Feature/Api/V1/ApiAuthenticationTest.php#L10) |
| AUTH-02 | Invalid key | `GET /api/v1/files` with `X-API-Key: sk_live_<random>` (not in DB) | **401** `error.code = invalid_api_key` | — | [`test_request_with_invalid_api_key_is_rejected_with_401`](../tests/Feature/Api/V1/ApiAuthenticationTest.php#L19) |
| AUTH-03 | Revoked key | key exists but `is_active = false` | **401** `error.code = invalid_api_key` | — | [`test_request_with_revoked_api_key_is_rejected_with_401`](../tests/Feature/Api/V1/ApiAuthenticationTest.php#L28) |
| AUTH-04 | Creator attribution | `POST /api/v1/files` (payload without `created_by`) | **201** | `source_files.created_by` = the calling key's id | [`test_stored_resource_carries_calling_api_key_as_creator`](../tests/Feature/Api/V1/ApiAuthenticationTest.php#L37) |
| AUTH-05 | Valid key + usage tracking | `GET /api/v1/files` with valid `X-API-Key` | **200** | `api_keys.last_used_at` is set | [`test_request_with_valid_api_key_is_allowed_and_records_usage`](../tests/Feature/Api/V1/ApiAuthenticationTest.php#L53) |
| AUTH-06 | Bearer alternative | same as AUTH-05 but via `Authorization: Bearer <key>` | **200** | — | [`test_bearer_authorization_header_is_accepted`](../tests/Feature/Api/V1/ApiAuthenticationTest.php#L60) |

**Acceptance rule for every other layer:** unless a scenario says otherwise, all requests below carry a valid `X-API-Key`.

## 4. Layer 2 — CRUD contract (all 25 resources)

One protocol, executed per resource by [`CrudApiTestCase`](../tests/Feature/Api/V1/CrudApiTestCase.php). Each run uses a freshly migrated database and a factory-seeded record.

| ID | Scenario | Request | Expected | State check |
|---|---|---|---|---|
| CRUD-01 | List | `GET /api/v1/<resource>` | **200**, `data[0].id` = seeded id (paginated: `data` + `meta`) | row present |
| CRUD-02 | Create | `POST /api/v1/<resource>` with the record's full attribute set (unique columns overridden per resource, see matrix) | **201**, `data.id` returned | `assertDatabaseHas(<table>, [id])` |
| CRUD-03 | Show | `GET /api/v1/<resource>/{id}` | **200**, `data.id` matches | — |
| CRUD-04 | Partial update | `PATCH /api/v1/<resource>/{id}` with the resource-specific field (matrix) | **200** | `assertDatabaseHas(<table>, [id, field])` |
| CRUD-05 | Delete | `DELETE /api/v1/<resource>/{id}` | **204** | soft-delete table → row remains with `deleted_at`; otherwise row gone |

### Per-resource matrix

URLs and tables are authoritative from [`routes/api.php`](../routes/api.php). "Store override" / "Update field" are the exact per-resource hooks used by CRUD-02/CRUD-04 (they exist because of unique columns or required FKs).

| Resource (route prefix) | Test class | Table | Store override (CRUD-02) | Update field (CRUD-04) | Soft delete (CRUD-05) |
|---|---|---|---|---|---|
| `/files` | [`SourceFileApiTest`](../tests/Feature/Api/V1/SourceFileApiTest.php) | `source_files` | `external_ref` (unique) | `page_count: 42` | ✔ (row kept, `deleted_at` set) |
| `/prompts` | [`MasterPromptApiTest`](../tests/Feature/Api/V1/MasterPromptApiTest.php) | `prompts` | `family_id` (fresh uuid), `version: 2.0` | `purpose` | ✔ |
| `/outputs` | [`ProcessedOutputApiTest`](../tests/Feature/Api/V1/ProcessedOutputApiTest.php) | `processed_outputs` | — | `token_count: 555` | ✔ |
| `/cleaned-outputs` | [`CleanedOutputApiTest`](../tests/Feature/Api/V1/CleanedOutputApiTest.php) | `cleaned_outputs` | — | `quality_score: 88.5` | ✔ |
| `/models` | [`AiModelApiTest`](../tests/Feature/Api/V1/AiModelApiTest.php) | `models` | `code` (unique) | `name_fa` | — |
| `/output-types` | [`OutputTypeApiTest`](../tests/Feature/Api/V1/OutputTypeApiTest.php) | `output_types` | `code` (unique) | `name_fa` | — |
| `/automation-actions` | [`AutomationActionApiTest`](../tests/Feature/Api/V1/AutomationActionApiTest.php) | `automation_actions` | `code` (unique) | `name_fa` | — |
| `/automation-flows` | [`AutomationFlowApiTest`](../tests/Feature/Api/V1/AutomationFlowApiTest.php) | `automation_flows` | `platform_flow_id: null` | `description` | — |
| `/services` | [`ServiceRegistryApiTest`](../tests/Feature/Api/V1/ServiceRegistryApiTest.php) | `service_registry` | — | `name` | — |
| `/jobs` | [`AutomationJobApiTest`](../tests/Feature/Api/V1/AutomationJobApiTest.php) | `automation_jobs` | — | `priority: 9` | — |
| `/ground-truth` | [`HumanGroundTruthApiTest`](../tests/Feature/Api/V1/HumanGroundTruthApiTest.php) | `human_ground_truth` | — | `approval_notes` | — |
| `/benchmark-sessions` | [`BenchmarkSessionApiTest`](../tests/Feature/Api/V1/BenchmarkSessionApiTest.php) | `benchmark_sessions` | — | `name`, `description` | — |
| `/benchmark-results` | [`BenchmarkResultApiTest`](../tests/Feature/Api/V1/BenchmarkResultApiTest.php) | `benchmark_results` | `candidate_cleaned_id`, `baseline_cleaned_id`, `ground_truth_id` → null | `overall_score: 99.5` | — |
| `/datasets` | [`DatasetApiTest`](../tests/Feature/Api/V1/DatasetApiTest.php) | `datasets` | `name`, `created_by: null` | `description` | — |
| `/dataset-items` | [`DatasetItemApiTest`](../tests/Feature/Api/V1/DatasetItemApiTest.php) | `dataset_items` | `input_cleaned_id`, `ground_truth_id` → null | `sequence_order: 77` | — |
| `/training-jobs` | [`TrainingJobApiTest`](../tests/Feature/Api/V1/TrainingJobApiTest.php) | `training_jobs` | 6 nullable fields → null | `progress_percent: 55` | — |
| `/trained-models` | [`TrainedModelApiTest`](../tests/Feature/Api/V1/TrainedModelApiTest.php) | `trained_models` | `training_job_id: null`, `name` (unique pair), `version: 2.0.0` | `service_endpoint` | — |
| `/model-evaluations` | [`ModelEvaluationApiTest`](../tests/Feature/Api/V1/ModelEvaluationApiTest.php) | `model_evaluations` | 4 nullable fields → null | `overall_score: 87.25` | — |
| `/model-releases` | [`ModelReleaseApiTest`](../tests/Feature/Api/V1/ModelReleaseApiTest.php) | `model_releases` | 6 nullable fields → null | `release_notes` | — |
| `/release-reports` | [`ReleaseReportApiTest`](../tests/Feature/Api/V1/ReleaseReportApiTest.php) | `release_reports` | — | `storage_path` | — |
| `/vector-collections` | [`VectorCollectionApiTest`](../tests/Feature/Api/V1/VectorCollectionApiTest.php) | `vector_collections` | `name` (unique) | `description` | — |
| `/vector-collections/{id}/items` (sub-resource) | [`VectorCollectionItemApiTest`](../tests/Feature/Api/V1/VectorCollectionItemApiTest.php) | `vector_collection_items` | `cleaned_output_id: null` | `external_vector_id` (uuid) | — |
| `/feedbacks` | [`FeedbackLogApiTest`](../tests/Feature/Api/V1/FeedbackLogApiTest.php) | `feedback_logs` | 5 nullable fields → null | `comment` | — |
| `/service-call-logs` | [`ServiceCallLogApiTest`](../tests/Feature/Api/V1/ServiceCallLogApiTest.php) | `service_call_logs` | `error_message: null` | `http_status: 201` | — |
| `/metadata-schemas` | [`MetadataSchemaApiTest`](../tests/Feature/Api/V1/MetadataSchemaApiTest.php) | `metadata_schemas` | `name` (unique slug) | `scope: mp3` | — |

Notes:
- The items sub-resource is parent-scoped: `collectionUrl` builds `api/v1/vector-collections/{vector_collection_id}/items`, so CRUD-01…05 also prove the scoping.
- `store` payloads are the seed record's own attributes (`attributesToArray()`, minus `id`/timestamps) merged with the overrides — i.e. the Form Request (`Store*Request`) accepts a real-world full payload, not a toy one. `update` (CRUD-04) is a genuine **partial** update (one field only), proving `Update*Request` makes everything optional.
- Soft-deleted resources are exactly four: `source_files`, `prompts`, `processed_outputs`, `cleaned_outputs`.

### Manual (curl) acceptance for any resource

```bash
# 1) list
curl -s http://localhost:8000/api/v1/models -H "X-API-Key: $KEY" -H "Accept: application/json"

# 2) create (201)
curl -s -X POST http://localhost:8000/api/v1/models \
  -H "X-API-Key: $KEY" -H "Content-Type: application/json" -H "Accept: application/json" \
  -d '{"code":"my-model","name":"My Model","provider":"internal"}'

# 3) show (200)
curl -s http://localhost:8000/api/v1/models/<id> -H "X-API-Key: $KEY" -H "Accept: application/json"

# 4) partial update (200)
curl -s -X PATCH http://localhost:8000/api/v1/models/<id> \
  -H "X-API-Key: $KEY" -H "Content-Type: application/json" -H "Accept: application/json" \
  -d '{"name_fa":"مدل من"}'

# 5) delete (204; for /files, /prompts, /outputs, /cleaned-outputs the row stays with deleted_at)
curl -s -X DELETE http://localhost:8000/api/v1/models/<id> -H "X-API-Key: $KEY"
```

## 5. Layer 3a — File upload & download ([`FileUploadTest`](../tests/Feature/Api/V1/FileUploadTest.php))

Implemented by [`FileUploadService`](../app/Services/FileUploadService.php) behind `POST /api/v1/files/upload` and `GET /api/v1/files/{id}/download` ([`SourceFileController`](../app/Http/Controllers/Api/V1/SourceFileController.php)). Rules come from [`config/ai-factory.php`](../config/ai-factory.php) (`upload.allowed` extension→mime whitelist, `upload.max_bytes`).

### Happy path

| ID | Scenario | Request (multipart) | Expected | State check | Test anchor |
|---|---|---|---|---|---|
| UPLOAD-01 | Word document | `file` = 12 KB `sample.docx` (docx mime) + `metadata[speaker]=Dr. Ali` | **201**; `data.file_type=docx`, `processing_status=registered`, `original_filename=sample.docx`, `file_size_bytes=12288`, `metadata.speaker`, `created_by=<key id>`, top-level `duplicate:false`, non-empty `checksum_sha256` + `storage_path` | DB row matches; **binary exists on disk** at `storage_path` | [`test_word_document_upload_registers_source_file`](../tests/Feature/Api/V1/FileUploadTest.php#L29) |
| UPLOAD-02 | PDF / TXT / MP3 / MP4 | 4 uploads, unique content each, declared mime per type | **201** each; `file_type` = extension (`pdf`, `txt`, `mp3`, `mp4`); `processing_status=registered` | 4 rows | [`test_pdf_txt_audio_and_video_upload_all_register`](../tests/Feature/Api/V1/FileUploadTest.php#L60) |
| UPLOAD-03 | Multipart metadata fields | `file` + `external_ref=upload-001` + `language=en` + `metadata[speaker]/metadata[topic]` | **201**; all four reflected in `data` | row carries them | [`test_multipart_metadata_fields_are_collected`](../tests/Feature/Api/V1/FileUploadTest.php#L82) |
| UPLOAD-04 | Checksum dedupe | same bytes uploaded twice as `one.mp3` then `two.mp3` | 1st: **201** `duplicate:false`; 2nd: **200** `duplicate:true`, **same `data.id`**, original filename `one.mp3` kept | exactly **1** row for that checksum | [`test_uploading_identical_content_returns_existing_file_as_duplicate`](../tests/Feature/Api/V1/FileUploadTest.php#L102) |
| UPLOAD-05 | Download round-trip | upload `lecture.mp3` (known bytes), then `GET /api/v1/files/{id}/download` | **200**; `Content-Disposition: attachment; filename=lecture.mp3`; **streamed bytes identical** to uploaded content | — | [`test_uploaded_file_can_be_downloaded_with_original_name_and_mime`](../tests/Feature/Api/V1/FileUploadTest.php#L119) |
| UPLOAD-06 | Binary lost on disk | row with `storage_path` but no file on disk | **410** `{ "error": { "code": "file_unavailable" } }` | — | [`test_download_returns_410_when_binary_is_missing_from_disk`](../tests/Feature/Api/V1/FileUploadTest.php#L133) |

### Negative matrix (upload)

| ID | Scenario | Request | Expected | Test anchor |
|---|---|---|---|---|
| UPLOAD-07 | No file part | `POST /files/upload` without `file` | **422**, validation error on `file` | [`test_upload_without_file_is_422`](../tests/Feature/Api/V1/FileUploadTest.php#L144) |
| UPLOAD-08 | Empty file | 0-byte `empty.mp3` | **422** `error.code = file_empty` | [`test_empty_file_is_422`](../tests/Feature/Api/V1/FileUploadTest.php#L151) |
| UPLOAD-09 | Oversized | `huge.mp3` = max_bytes + 1 KB | **413** `error.code = file_too_large` | [`test_oversized_file_is_413`](../tests/Feature/Api/V1/FileUploadTest.php#L158) |
| UPLOAD-10 | Unknown extension | `malware.exe` | **415** `error.code = unsupported_file_type` | [`test_unknown_extension_is_415`](../tests/Feature/Api/V1/FileUploadTest.php#L168) |
| UPLOAD-11 | Mime/extension mismatch | `actually-exe.mp3` declared as `application/x-msdownload` | **415** `error.code = mime_mismatch` | [`test_mime_extension_mismatch_is_415`](../tests/Feature/Api/V1/FileUploadTest.php#L175) |
| UPLOAD-12 | Hostile filename sanitized | `..\..\evil<script>.mp3` | **201**; `original_filename` stripped of traversal; stored path stays **inside `uploads/`** (no `..` in directory) | `assertStringNotContainsString('..', dirname($stored))` | [`test_weird_filename_is_sanitized_before_storage`](../tests/Feature/Api/V1/FileUploadTest.php#L182) |
| UPLOAD-13 | Duplicate `external_ref` | row with `external_ref=taken-001` exists; upload again with same ref | **422**, validation error on `external_ref` | [`test_duplicate_external_ref_is_422`](../tests/Feature/Api/V1/FileUploadTest.php#L194) |

### Metadata-schema enforcement on upload (US-08)

Schema definition shape: `{ key: { type: string|integer|boolean|object, required: bool, enum?: [], additional?: allow|reject } }` with `scope` = `global` or a `file_type` (see [`MetadataValidator`](../app/Support/MetadataValidator.php), resolution in [`MetadataSchemaResolver`](../app/Support/MetadataSchemaResolver.php)).

| ID | Scenario | Setup → Request | Expected | Test anchor |
|---|---|---|---|---|
| UPLOAD-14 | Scope-matched schema enforced | schema `scope=mp3` requiring `speaker:string` → upload `ok.mp3` with `metadata[speaker]=Ali`; then `no-speaker.mp3` without it | 1st **201**; 2nd **422** `error.code = metadata_invalid` + message `metadata.speaker is required by the schema.` | [`test_upload_metadata_is_validated_against_file_type_scope_schema`](../tests/Feature/Api/V1/FileUploadTest.php#L205) |
| UPLOAD-15 | Type mismatch | same schema → `metadata[speaker]=123` | **422** `metadata_invalid` + `metadata.speaker must be a string.` | [`test_upload_metadata_type_mismatch_is_422`](../tests/Feature/Api/V1/FileUploadTest.php#L225) |
| UPLOAD-16 | Unknown field rejected | same schema → `metadata[speaker]=Ali, metadata[hacker]=x` | **422** + `metadata.hacker is not allowed by the schema.` | [`test_upload_metadata_unknown_field_is_422`](../tests/Feature/Api/V1/FileUploadTest.php#L240) |
| UPLOAD-17 | Explicit schema by name + enum | schema `course-files` (global, `course` required, `enum: [db, web]`) → upload `named.txt` with `metadata_schema=course-files` | `course=ai` → **422** `metadata.course must be one of: db, web.`; `course=db` → **201** | [`test_upload_explicit_metadata_schema_by_name_is_enforced`](../tests/Feature/Api/V1/FileUploadTest.php#L254) |
| UPLOAD-18 | Unknown explicit schema | `metadata_schema=does-not-exist` | **422** `error.code = metadata_schema_not_found` | [`test_upload_with_unknown_explicit_metadata_schema_is_422`](../tests/Feature/Api/V1/FileUploadTest.php#L278) |

## 6. Layer 3b — Metadata query & validation on JSON endpoints ([`MetadataFilesApiTest`](../tests/Feature/Api/V1/MetadataFilesApiTest.php))

| ID | Scenario | Setup → Request | Expected | State check | Test anchor |
|---|---|---|---|---|---|
| META-01 | Filter by metadata field | 2 files, one with `metadata={speaker: "Dr. Ali", topic: "Databases"}` → `GET /files?metadata[speaker]=Dr. Ali`, `?metadata[topic]=Databases`, `?metadata[topic]=Networking`, `?metadata[language]=fa` | **200** with **1**, **1**, **0**, **0** rows respectively (a plain column like `language` is *not* a metadata key and must not match) | — | [`test_files_can_be_filtered_by_metadata_field`](../tests/Feature/Api/V1/MetadataFilesApiTest.php#L12) |
| META-02 | Combined filters | 2 files sharing `speaker=Ali`, different topics → `?metadata[speaker]=Ali&metadata[topic]=Databases` | **200**, **1** row (AND semantics) | — | [`test_multiple_metadata_filters_are_combined`](../tests/Feature/Api/V1/MetadataFilesApiTest.php#L37) |
| META-03 | Store violates global schema | global schema requiring `speaker` → `POST /files {file_type: pdf, metadata: {no_speaker_here: "true"}}` | **422** on `metadata` + `metadata.speaker is required by the schema.` | `source_files` count stays **0** (nothing persisted) | [`test_store_file_with_metadata_violating_global_schema_is_422`](../tests/Feature/Api/V1/MetadataFilesApiTest.php#L48) |
| META-04 | Store type mismatch | schema `scope=pdf` → `POST /files {file_type: pdf, metadata: {speaker: 123}}` | **422** + `metadata.speaker must be a string.` | — | [`test_store_file_metadata_type_mismatch_is_422`](../tests/Feature/Api/V1/MetadataFilesApiTest.php#L71) |
| META-05 | Explicit `metadata_schema_id` enforced | schema `scope=pdf` requiring `instructor` → `POST /files {metadata_schema_id, metadata: {other: value}}`; then with `{instructor: "Dr. Ali"}` | 1st **422** + `metadata.instructor is required by the schema.`; 2nd **201** with `data.metadata_schema_id` set | — | [`test_store_file_with_explicit_metadata_schema_id_is_enforced`](../tests/Feature/Api/V1/MetadataFilesApiTest.php#L90) |
| META-06 | Update validates too | global schema requiring `speaker` → `PATCH /files/{id}` with `{metadata: {speaker: 99}}`; then `{speaker: "Reza"}` | 1st **422** on `metadata`; 2nd **200** `data.metadata.speaker = Reza` | — | [`test_update_file_metadata_against_schema_is_validated`](../tests/Feature/Api/V1/MetadataFilesApiTest.php#L118) |

## 7. Status-code reference (what a tester will see)

| Code | Meaning | Triggered by |
|---|---|---|
| 200 | OK / duplicate replay / partial update | CRUD-01/03/04, UPLOAD-04 (2nd upload), META happy paths |
| 201 | Created | CRUD-02, UPLOAD-01…03/12/14/17 |
| 204 | No content (delete) | CRUD-05 |
| 401 | Auth failure | AUTH-01 (`unauthenticated`), AUTH-02/03 (`invalid_api_key`) |
| 410 | Gone (binary missing on disk) | UPLOAD-06 (`file_unavailable`) |
| 413 | Payload too large | UPLOAD-09 (`file_too_large`) |
| 415 | Unsupported media | UPLOAD-10 (`unsupported_file_type`), UPLOAD-11 (`mime_mismatch`) |
| 422 | Validation failure | CRUD invalid payloads (Form Request), UPLOAD-07/08/13, all schema failures (`metadata_invalid` / `metadata_schema_not_found`), META-03…06 |

Error envelope (non-2xx): `{ "error": { "code": "<machine_code>", "message": "..." } }` — rendered by [`AppServiceProvider`](../app/Providers/AppServiceProvider.php) (upload/metadata exceptions) and the standard 422 validator payload for Form Request failures.

## 8. Acceptance (delivery) checklist — 0 → 100

Work top-down; each item names the command that proves it.

- [ ] **Layer 0 — harness**: `php artisan test --compact` runs green end-to-end; `APP-01` (`GET /` → 200) first.
- [ ] **Layer 1 — auth**: all six AUTH-xx pass → `php artisan test --compact --filter=ApiAuthenticationTest`. Spot-check AUTH-04/05 manually: create a key with `php artisan api-key:create demo`, call `GET /api/v1/files`, then `php artisan api-key:list` to see `last_used_at` move.
- [ ] **Layer 2 — CRUD**: all 125 CRUD tests pass. Manually walk the 5-step curl script in §4 for at least one soft-delete resource (`/files`) and one hard-delete resource (e.g. `/models`), and verify the soft-deleted row is invisible in `GET /files` but still in the table with `deleted_at`.
- [ ] **Layer 3a — upload/download**: all 18 UPLOAD-xx pass. Manual round-trip: upload a real PDF via Postman/curl (multipart `file`), check `data.storage_path` exists under `storage/app/private/uploads/`, download it, compare SHA-256 of the two byte streams; re-upload the same file → expect **200** + `duplicate: true`; try a 513 MB file → 413; a `.exe` → 415.
- [ ] **Layer 3b — metadata**: all 6 META-xx pass. Manual: `POST /api/v1/metadata-schemas` a `scope=pdf` schema requiring `speaker`; `POST /files` a pdf-shaped record without it → 422 with the named key; with it → 201; `GET /files?metadata[speaker]=...` filters.
- [ ] **No regressions**: full suite green again — `php artisan test --compact` → **157 passed**.
- [ ] **Style gate**: `php vendor/bin/pint --dirty --format agent` reports no fixes.

### Known gaps (do NOT accept as shipped)

The plan's EP-03…EP-14 (job engine, processing, dataset generate/export, training run, benchmark run, lineage, prompt resolve, `PipelineE2eTest`) are **not implemented yet** — their scenarios live in [`acceptance-delivery-plan.md`](acceptance-delivery-plan.md) §6 and must be re-verified against tests when those phases land. The current acceptance surface is exactly Layers 0–3b above.

## 9. Related documents

| Document | Relation |
|---|---|
| [`README.md`](../README.md) | Project overview, architecture, setup, run instructions — this handbook is linked from its **Testing** section |
| [`acceptance-delivery-plan.md`](acceptance-delivery-plan.md) | The forward-looking delivery contract (phases 1–8); this doc is the *already-shipped* half of it (phases 1–2) |
| [`core-ai-factory-schema-api.md`](core-ai-factory-schema-api.md) | Schema + base CRUD API reference (24 tables, route list) |
| [`end to end test api core db ai.md`](end%20to%20end%20test%20api%20core%20db%20ai.md) | Original black-box backlog (epics/user stories US-01…US-34) that seeds the scenario IDs used here |
| [`.ai/rules/general.md`](../.ai/rules/general.md), [`.ai/rules/models.md`](../.ai/rules/models.md), [`.ai/rules/database.md`](../.ai/rules/database.md) | Project invariants the scenarios encode (e.g. `created_by` auto-fill, output binding, `quality_rate` 0–100) |
