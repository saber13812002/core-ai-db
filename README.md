# Core AI Factory — Database & REST API

A Laravel 13 backend for the **Core AI Factory**: a pipeline that ingests source files (lecture PDFs, DOCX, audio/video), runs AI automation actions (transcript extraction, slide extraction, quiz generation, summarization, text cleaning) against registered LLMs, produces versioned outputs, supports human review and ground truth, benchmarks candidate vs. baseline models, builds training datasets, tracks fine-tuning jobs / trained models / releases, and logs feedback and external service calls.

Everything is exposed as a **versioned REST API** (`/api/v1`) with full CRUD for every domain entity, plus file upload/download and metadata schemas — **124 endpoints**, all behind **API-key authentication** (`X-API-Key`). A test-driven acceptance map of everything shipped lives in [`plans/e2e-test-coverage.md`](plans/e2e-test-coverage.md).

## Tech Stack

| Layer | Technology |
|---|---|
| Framework | Laravel 13 (PHP 8.5) |
| Database | PostgreSQL 16 (dev: WSL distro `coreaipg` at `127.0.0.1:5432`) |
| Tests | PHPUnit feature tests on SQLite `:memory:` |
| Style | Laravel Pint |
| Frontend | None (API-only) |

## Architecture

Strict layering, one direction of dependency:

```
Controller → Service → Repository → Eloquent (models)
```

- [`app/Http/Controllers/Api/V1`](app/Http/Controllers/Api/V1) — 24 thin controllers (route-model-bound, eager-load relations, delegate to a service, respond via an API Resource).
- [`app/Http/Requests/Api/V1`](app/Http/Requests/Api/V1) — 48 Form Requests (`Store*Request` + `Update*Request`, the latter extends the former and makes all rules optional).
- [`app/Http/Resources/Api/V1`](app/Http/Resources/Api/V1) — 24 API Resources.
- [`app/Services`](app/Services) — 24 services extending [`BaseService`](app/Services/BaseService.php) (`list / create / find / findOrThrow / update / delete`). `VectorCollectionItemService` exposes collection-scoped variants (`listForCollection`, `createForCollection`, `findInCollection`, `findOrThrowInCollection`, `updateInCollection`, `deleteInCollection`).
- [`app/Repositories`](app/Repositories) — 24 repositories extending [`BaseRepository`](app/Repositories/BaseRepository.php) (Eloquent query builder).
- [`app/Models`](app/Models) — 24 models with relations, casts, `#[Fillable]`.

## Domain Model (24 tables)

### Reference data
| Table | Model | Notes |
|---|---|---|
| `output_types` | `OutputType` | bigint PK |
| `automation_actions` | `AutomationAction` | bigint PK; `input_file_types` jsonb; unique `code` |
| `prompts` | `MasterPrompt` | versioned master prompts; soft delete; unique `(family_id, version)` |
| `models` | `AiModel` | registered LLMs; bigint PK; unique `code` |
| `automation_flows` | `AutomationFlow` | external orchestration-platform flows |
| `service_registry` | `ServiceRegistry` | external micro-services; bigint PK |

### Pipeline
| Table | Model | Notes |
|---|---|---|
| `source_files` | `SourceFile` | ingested documents; soft delete; supersedes via self FK |
| `automation_jobs` | `AutomationJob` | one execution of an action on a file with a model+prompt |
| `processed_outputs` | `ProcessedOutput` | versioned AI outputs; `is_latest`, `superseded_by_id`; soft delete |
| `cleaned_outputs` | `CleanedOutput` | cleaned variants of processed outputs; soft delete |
| `human_ground_truth` | `HumanGroundTruth` | approved human references per file+output-type |
| `vector_collections` / `vector_collection_items` | `VectorCollection` / `VectorCollectionItem` | RAG collections and their items |

### Benchmarking & training
| Table | Model | Notes |
|---|---|---|
| `benchmark_sessions` | `BenchmarkSession` | `ab` or `vs-ground-truth` sessions |
| `benchmark_results` | `BenchmarkResult` | candidate vs. baseline (and/or ground truth) scores |
| `datasets` / `dataset_items` | `Dataset` / `DatasetItem` | fine-tuning datasets (train/validation/test split) |
| `training_jobs` | `TrainingJob` | fine-tuning runs on a dataset via a service |
| `trained_models` | `TrainedModel` | produced models; unique `(name, version)` |
| `model_evaluations` | `ModelEvaluation` | scored evaluations of trained models |
| `model_releases` / `release_reports` | `ModelRelease` / `ReleaseReport` | release lifecycle + report artifacts |

### Telemetry
| Table | Model | Notes |
|---|---|---|
| `feedback_logs` | `FeedbackLog` | like/dislike/correction/flag; bigint PK |
| `service_call_logs` | `ServiceCallLog` | external service call audit; bigint PK |

**Soft deletes** exist on exactly four tables: `source_files`, `prompts`, `processed_outputs`, `cleaned_outputs`.

**PostgreSQL views** (created by [`2026_09_17_000025_create_factory_views.php`](database/migrations/2026_09_17_000025_create_factory_views.php), pgsql-only):
- `v_latest_outputs` — current, non-superseded, non-deleted outputs.
- `v_execution_matrix` — files × active actions × output/job/model/prompt status.
- `v_benchmark_comparison` — benchmark results with model codes joined in.

## REST API

All routes live under `/api/v1` in [`routes/api.php`](routes/api.php). Each resource has `index`, `store`, `show`, `update`, `destroy` (120 routes total), plus the extras below (124 routes). Responses are JSON: a `data` payload (wrapped in `data`/`meta` for paginated `index`) with standard HTTP codes (`200`, `201`, `204`, `404`, `413`, `415`, `422`). Every request requires an `X-API-Key` header (or `Authorization: Bearer <key>`), managed via `php artisan api-key:create|list|revoke`.

| Route | Model |
|---|---|
| `GET/POST /api/v1/files` · `GET/PATCH/DELETE /api/v1/files/{file}` | `SourceFile` |
| `/api/v1/output-types` | `OutputType` |
| `/api/v1/automation-actions` | `AutomationAction` |
| `/api/v1/prompts` | `MasterPrompt` |
| `/api/v1/models` | `AiModel` |
| `/api/v1/automation-flows` | `AutomationFlow` |
| `/api/v1/services` | `ServiceRegistry` |
| `/api/v1/jobs` | `AutomationJob` |
| `/api/v1/outputs` | `ProcessedOutput` |
| `/api/v1/cleaned-outputs` | `CleanedOutput` |
| `/api/v1/ground-truth` | `HumanGroundTruth` |
| `/api/v1/benchmark-sessions` | `BenchmarkSession` |
| `/api/v1/benchmark-results` | `BenchmarkResult` |
| `/api/v1/datasets` | `Dataset` |
| `/api/v1/dataset-items` | `DatasetItem` |
| `/api/v1/training-jobs` | `TrainingJob` |
| `/api/v1/trained-models` | `TrainedModel` |
| `/api/v1/model-evaluations` | `ModelEvaluation` |
| `/api/v1/model-releases` | `ModelRelease` |
| `/api/v1/release-reports` | `ReleaseReport` |
| `/api/v1/vector-collections` | `VectorCollection` |
| `/api/v1/vector-collections/{vectorCollection}/items` | `VectorCollectionItem` (sub-resource) |
| `/api/v1/feedbacks` | `FeedbackLog` |
| `/api/v1/service-call-logs` | `ServiceCallLog` |
| `/api/v1/metadata-schemas` | `MetadataSchema` |
| `POST /api/v1/files/upload` · `GET /api/v1/files/{file}/download` | binary upload (multipart) + streamed download, dedupe by SHA-256 |

`index` accepts `?per_page=N` (default 15). `store`/`update` are validated by Form Requests; `update` accepts any subset of the store fields (partial updates). The `items` sub-resource is scoped: an item is always resolved inside its `vector-collections/{vectorCollection}` parent.

### Example

```bash
# List files
curl http://localhost:8000/api/v1/files

# Create a source file
curl -X POST http://localhost:8000/api/v1/files \
  -H "Content-Type: application/json" -H "Accept: application/json" \
  -d '{"external_ref":"lecture-001","file_type":"pdf","original_filename":"lecture.pdf","storage_path":"files/lecture.pdf","checksum":"abc123"}'

# Show / update / delete
curl http://localhost:8000/api/v1/files/{id}
curl -X PATCH http://localhost:8000/api/v1/files/{id} -H "Accept: application/json" -d '{"page_count":42}'
curl -X DELETE http://localhost:8000/api/v1/files/{id}
```

## Local Setup

1. **Database** — PostgreSQL 16. Dev setup here runs in a WSL1 distro `coreaipg` (role `root`, trust auth, database `core_ai_db` on `127.0.0.1:5432`).
2. **Environment** — copy `.env.example` to `.env`; set `DB_CONNECTION=pgsql`, `DB_HOST=127.0.0.1`, `DB_PORT=5432`, `DB_DATABASE=core_ai_db`, `DB_USERNAME=root`.
3. **Install & build**:

   ```bash
   composer install
   php artisan migrate:fresh --seed
   php artisan serve        # or: npm run dev
   ```

4. **Seeders** — `ReferenceDataSeeder` (run by `DatabaseSeeder`) idempotently seeds reference data by natural key: output types, automation actions, registered models, services, automation flows, and master prompt families.

## Testing

```bash
php artisan test            # 157 tests (441 assertions), SQLite :memory: via RefreshDatabase
```

[`tests/Feature/Api/V1`](tests/Feature/Api/V1) contains an abstract [`CrudApiTestCase`](tests/Feature/Api/V1/CrudApiTestCase.php) with five generic CRUD tests (list / create / show / update / delete, including soft-delete handling) plus one thin subclass per resource supplying model class, table name, URL, and per-resource payload overrides, on top of dedicated scenario suites: [`ApiAuthenticationTest`](tests/Feature/Api/V1/ApiAuthenticationTest.php) (API-key auth), [`FileUploadTest`](tests/Feature/Api/V1/FileUploadTest.php) (upload / download / dedupe / negative matrix), and [`MetadataFilesApiTest`](tests/Feature/Api/V1/MetadataFilesApiTest.php) (metadata filter + schema validation).

**Acceptance handbook** — [`plans/e2e-test-coverage.md`](plans/e2e-test-coverage.md) maps every feature 0→100 to its exact test scenarios (request → expected response → visible state), layer by layer, with per-scenario links to the test methods and a delivery checklist. The forward-looking plan for the not-yet-implemented pipeline phases (job engine, dataset/training/benchmark execution, lineage) is [`plans/acceptance-delivery-plan.md`](plans/acceptance-delivery-plan.md).

## Code Style

Laravel Pint (config in [`pint.json`](pint.json)):

```bash
php vendor/bin/pint --dirty --format agent
```

## Repository Layout

```
app/
  Http/Controllers/Api/V1/   # 24 API controllers
  Http/Requests/Api/V1/      # 48 Store/Update Form Requests
  Http/Resources/Api/V1/     # 24 API Resources
  Models/                    # 24 Eloquent models
  Repositories/              # BaseRepository + 24 repositories
  Services/                  # BaseService + 24 services
database/
  factories/                 # 24 factories (+ UserFactory)
  migrations/                # 24 table migrations + pgsql views
  seeders/                   # DatabaseSeeder + ReferenceDataSeeder
plans/                       # implementation plans
routes/
  api.php                    # all /api/v1 routes
tests/Feature/Api/V1/        # abstract base + 24 per-resource tests
```

## Status & Roadmap

- [x] Schema for all 22 design-deepseek tables + `vector_collections` / `vector_collection_items`
- [x] Models, repositories, services, Form Requests, Resources, controllers
- [x] 120 REST CRUD endpoints
- [x] API-key authentication (all `/api/v1` routes)
- [x] File upload / download + metadata schemas (`metadata_schemas`, JSONB filter, schema validation on store/update)
- [x] Feature tests (157 passing) — acceptance map: [`plans/e2e-test-coverage.md`](plans/e2e-test-coverage.md)
- [ ] Actual AI automation execution (jobs currently only tracked via API)
- [ ] Dataset generate/export, training run, benchmark run, lineage (see [`plans/acceptance-delivery-plan.md`](plans/acceptance-delivery-plan.md) phases 5–8)
- [ ] Vector embedding population (collections are tracked, not populated)
