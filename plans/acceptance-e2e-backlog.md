# Core AI Factory — Backlog، شرایط پذیرش و سناریوهای E2E

> سند پذیرشِ پروژه: هر نقطه‌ی خواسته‌شده (از یادداشت‌های صوتی کارفرما) به‌صورت **User Story + Acceptance Criteria** تعریف شده و **به یک تست E2E خودکارِ تک‌سناریو در [`PipelineE2eTest`](../tests/Feature/Api/V1/PipelineE2eTest.php) یک‌به‌یک گره خورده** — یعنی هر سناریو *تک‌تک* و *مستقل* اجرا می‌شود. داده‌های مرجع برای اجرای صفر‌به‌سناریو در [`ReferenceDataSeeder`](../database/seeders/ReferenceDataSeeder.php) سید شده است.
>
> **حالت اجرا:** `php artisan test --compact` → **198 تست / 652 assertion، سبز** (SQLite :memory:، سکیوریتی کامل از روی همان مایگریشن‌های واقعی).
> **حالت اجرای دستی صفر‌به‌سناریو:** `php artisan migrate:fresh && php artisan db:seed` و سپس با یک API key (`php artisan api-key:create demo`) هر سناریو از طریق curl/Postman قابل تکرار است.

---

## ۱. Product Backlog — User Stories + Acceptance Criteria

هر Feature یک story است؛ ACها به‌شکل Given/When/Then نوشته شده‌اند و در ستون **Test anchor** دقیق‌اند تا یکبار، برای پذیرش، اجرا شوند.

### F-01 — ثبت فایل از طریق سرویس وب (Word/PDF/Excel/TXT/ویدیو/صوت)

> **User Story:** من به‌عنوان کاربر می‌خواهم فایل خود را از طریق سرویس وب (REST) آپلود کنم تا در دیتابیس ثبت شود، قابل مشاهده باشد و نتایج پردازش بعداً به آن متصل شوند.

| # | Acceptance Criteria (Given/When/Then) | شرایط پذیرش (فارسی) | Test anchor |
|---|---|---|---|
| AC-01.1 | **Given** a file of any supported type **When** `POST /api/v1/files/upload` (multipart) **Then** **201** and a `source_files` row exists with `file_type`, `processing_status=registered`, `created_by` = caller API key | آپلود هر نوع فایل مجاز → سطر جدید در `source_files` با نوع، وضعیت `registered` و شناسه‌ی کلید فراگیرنده | [`test_e2e_01_upload_registers_file_with_metadata_project_and_source_type`](../tests/Feature/Api/V1/PipelineE2eTest.php) |
| AC-01.2 | **When** the upload carries `metadata` (JSON key-value) and optional `project_id` / `source_type_id` **Then** all of them are persisted and visible in `data` (nested `data.project.name`, `data.source_type.code`) | متادیتای key-value + پروژه + نوع منبع در پاسخ و دیتابیس ثبت می‌شود | همان تست (اسرت‌های `data.metadata.*` و `data.project.name`) |
| AC-01.3 | **When** the seven real-world types docx / pdf / xlsx / txt / mp4 / mp3 / wav are uploaded **Then** all register, each with the correct `file_type`, and all appear in `GET /files` | هفت نوع واقعی (Word، PDF، Excel، TXT، ویدیو، MP3، WAV) همه ثبت و در لیست دیده می‌شوند | [`test_e2e_02_all_seven_file_types_upload_and_register`](../tests/Feature/Api/V1/PipelineE2eTest.php) |
| AC-01.4 | **When** the stored binary is requested via `GET /files/{id}/download` **Then** **200** with `Content-Disposition` and the original filename | فایل آپلودشده با نام اصلی قابل دانلود است | [`test_e2e_01_…`](../tests/Feature/Api/V1/PipelineE2eTest.php) (+ [`FileUploadTest`](../tests/Feature/Api/V1/FileUploadTest.php) UPLOAD-05) |
| AC-01.5 | **Given** a duplicate upload (same SHA-256) **When** uploaded again **Then** **200** + `duplicate: true`, same id, exactly one row | آپلود تکراری → پاسخ ۲۰۰ با `duplicate: true` و فقط یک سطر | [`FileUploadTest::test_uploading_identical_content_returns_existing_file_as_duplicate`](../tests/Feature/Api/V1/FileUploadTest.php) |
| AC-01.6 | **Given** unsupported extension / wrong mime / empty / oversized / hostile filename **When** uploaded **Then** **415 / 415 / 422 / 413** with machine error codes, filename sanitized into `uploads/` | انواع خطای آپلود (نوع نامعتبر، mime ناسازگار، خالی، بیش‌ازحد، نام مخرب) با کد خطای مشخص و sanitize نام | [`FileUploadTest`](../tests/Feature/Api/V1/FileUploadTest.php) UPLOAD-07…12 |
| AC-01.7 | **Given** the file list is queried **When** `GET /files?metadata[key]=value` **Then** only files with that exact metadata key are returned | فیلتر لیست فایل‌ها بر اساس کلیدهای متادیتا | [`MetadataFilesApiTest::test_files_can_be_filtered_by_metadata_field`](../tests/Feature/Api/V1/MetadataFilesApiTest.php) |

### F-02 — اسکیمای اختیاری متادیتا (JSON schema per file type)

> **User Story:** من می‌خواهم برای هر نوع فایل یک اسکیمای اختیاری تعریف کنم تا متادیتای آپلود/ثبت در برابر آن اعتبارسنجی شود و فایل‌ها بعداً با کلیدهای متادیتا پیدایشوند.

| # | Acceptance Criteria | شرایط پذیرش | Test anchor |
|---|---|---|---|
| AC-02.1 | **Given** a `metadata_schemas` row `scope=mp3` requiring `speaker:string` **When** an mp3 is uploaded missing the key **Then** **422** `metadata_invalid` naming the key | نبود کلید اجباری → ۴۲۲ با نام کلید | [`test_e2e_03_metadata_schema_enforced_on_upload_and_queryable`](../tests/Feature/Api/V1/PipelineE2eTest.php) |
| AC-02.2 | **When** the key has the wrong type **Then** **422** `metadata.speaker must be a string.` | نوع نادرست → ۴۲۲ با پیام مشخص | همان تست |
| AC-02.3 | **When** a valid payload is uploaded **Then** **201**, and `GET /files?metadata[speaker]=…` finds exactly that file | داده‌ی صحیح → ۲۰۱ و قابل پیداکردن با همان کلید متادیتا | همان تست |
| AC-02.4 | **Given** the JSON endpoints (`POST /files`, `PATCH /files/{id}`) **When** metadata violates the global/type schema or an explicit `metadata_schema_id` **Then** **422**, nothing persisted | همان اعتبارسنجی روی APIهای JSON و هنگام آپدیت | [`MetadataFilesApiTest`](../tests/Feature/Api/V1/MetadataFilesApiTest.php) META-03…06 |

### F-03 — وضعیت شفاف جاب (چه‌کسی، کی، با چه مدل، از چه منبع، خودکار یا دستی)

> **User Story:** من می‌خواهم برای هر جاب بدانم چه کسی/چه سرویسی آن را ساخت، کی صف شد، با چه مدل و اکشن، و آیا خودکار بود یا دستی — و بتوانم روی این فیلدها فیلتر بزنم.

| # | Acceptance Criteria | شرایط پذیرش | Test anchor |
|---|---|---|---|
| AC-03.1 | **Given** a manual job (`source=api`, `is_automatic=false`) and an automatic one (`source=scheduler`, `is_automatic=true`) **When** created **Then** both are stored with `status=queued`, `queued_at`, `created_by`, `source`, `is_automatic` | ثبت هر دو نوع جاب با تمام فیلدهای مبدأ (فرستنده، زمان صف‌شدن، مدل، اکشن، منبع، خودکار/دستی) | [`test_e2e_04_job_tracks_origin_model_action_and_automation`](../tests/Feature/Api/V1/PipelineE2eTest.php) |
| AC-03.2 | **When** `GET /jobs?source=scheduler` / `?is_automatic=0` (tri-state: absent → no filter) **Then** each filter returns exactly its own jobs | فیلتر لیست جاب‌ها بر اساس منبع و پرچم خودکار | همان تست |
| AC-03.3 | **When** `GET /jobs/{id}` **Then** the response shows nested `model`, `action`, `queued_at`, `source`, `is_automatic` | نمایش کامل وضعیت در show | همان تست |
| AC-03.4 | **Given** batch jobs **When** `POST /job-batches` (with estimates) then `POST /job-batches/{id}/close-out` **Then** actual tokens/durations aggregate and `status` becomes `completed` / `partially_failed`, `completed_at` set | چرخه‌ی batch: تخمین قبل، جمع‌بندی واقعی‌ها بعد | [`test_e2e_09_batch_close_out_aggregates_actuals`](../tests/Feature/Api/V1/PipelineE2eTest.php) + [`JobBatchApiTest`](../tests/Feature/Api/V1/JobBatchApiTest.php) |
| AC-03.5 | **Given** an external service **When** it reports on a file/job via `POST /webhooks/integration-events` **Then** the event is stored (existing reference required, wrong type rejected 404) and the full history is replayable per reference | گزارش‌دهی سرویس‌های خارجی + بازیابی تاریخچه‌ی کامل | [`test_e2e_08_integration_events_record_and_replay_history`](../tests/Feature/Api/V1/PipelineE2eTest.php) + [`IntegrationEventApiTest`](../tests/Feature/Api/V1/IntegrationEventApiTest.php) |

### F-04 — نتیجه‌ی جاب صف‌شده در دیتابیس (JSON) + زنجیره‌ی صوت

> **User Story:** من می‌خواهم وقتی جابی صف شد، نتیجه‌ی آن به‌صورت JSON در دیتابیس ذخیره شود، بدаниم id فایل اصلی، نوع اکشن و نوع خروجی چیست — و برای صوت: زیرنویس → زیرنویس اصلاح‌شده → متن کامل (با مدل‌ها/پرامپت‌های مختلف) → خلاصه، همه ردیابی‌پذیر باشند.

| # | Acceptance Criteria | شرایط پذیرش | Test anchor |
|---|---|---|---|
| AC-04.1 | **Given** a completed job on a source file **When** the worker records its result **Then** a `processed_outputs` row exists carrying `source_file_id`, `job_id`, `action_id`, `model_id`, `prompt_id`, `output_type_id` and the JSON/text content | هر خروجی = فایل × اکشن × مدل × پرامپت (ردیابی کامل) | [`test_e2e_05_audio_chain_results_are_stored_and_traceable`](../tests/Feature/Api/V1/PipelineE2eTest.php) |
| AC-04.2 | **Given** the audio chain (extract-transcript → refine-text → summarize) **When** each job completes **Then** each stage has its own `processed_outputs` row with its own output type, chained via `parent_job_id`, all tracing to the one original file | زنجیره‌ی صوت: زیرنویس → اصلاح → خلاصه، هر مرحله با مدل/پرامپت متفاوت، همه به یک فایل اصلی متصل | همان تست |
| AC-04.3 | **When** `GET /outputs?source_file_id=…` / `?output_type_id=…` / `?job_id=…` **Then** each filter isolates its rows | فیلتر خروجی‌ها بر اساس فایل اصلی، نوع خروجی و جاب | همان تست |
| AC-04.4 | **When** the data state is inspected **Then** the subtitle row carries its JSON result (`content_json`) and text stages carry `content_text` + `content_hash` | نتیجه‌ی JSON واقعاً در دیتابیس ذخیره می‌شود | همان تست (`assertDatabaseHas` + `content_json`) |
| AC-04.5 | **Given** immutable data policy **When** outputs/files are updated **Then** version rows are kept (`version_number`, `is_latest`) and deletes are soft where declared | عدم‌تغییرپذیری: نسخه‌ها نگه داشته می‌شوند، حذف نرم است | [`CrudApiTestCase`](../tests/Feature/Api/V1/CrudApiTestCase.php) (CRUD-05 soft) + [`ProcessedOutputFactory::superseded()`](../database/factories/ProcessedOutputFactory.php) |

### F-05 — ساخت دیتاست از فیلترهای متادیتا/خروجی + نسخه‌بندی

> **User Story:** من می‌خواهم با فیلتر کردن خروجی‌ها (نوع، تایید انسانی، متادیتای فایل) یک دیتاست بسازم و نسخه‌های بعدی‌اش به نسخه‌ی قبل اشاره کنند (مثل git).

| # | Acceptance Criteria | شرایط پذیرش | Test anchor |
|---|---|---|---|
| AC-05.1 | **Given** outputs of mixed types **When** `GET /outputs?output_type_id=…` **Then** exactly the matching rows are selected (verified id-by-id) | انتخاب دقیق خروجی‌های فیلترشده | [`test_e2e_06_dataset_built_from_filtered_outputs_and_versioned`](../tests/Feature/Api/V1/PipelineE2eTest.php) |
| AC-05.2 | **When** the selection is built into `POST /datasets` (v1, `version_number=1`, `filter_criteria` recorded) and items added via `/dataset-items` **Then** `GET /dataset-items?dataset_id=…` returns exactly those items | ساخت دیتاست v1 از خروجی‌های انتخاب‌شده + آیتم‌های قابل‌فیلتر | همان تست |
| AC-05.3 | **When** v2 is created with `previous_dataset_id` **Then** `GET /datasets/{v2}` shows `data.previous_dataset.id` = v1 | نسخه‌ی بعدی به قبلی گره خورده (git-like) | همان تست |

### F-06 — Fine-tune با دیتاست + ثبت مدل نهایی و بنچمارک

> **User Story:** من می‌خواهم با دیتاست مدل را fine-tune کنم و نتیجه‌ی training، مدل نهایی، امتیاز کلی و بنچمارک‌های جزئی (accuracy/fluency/…) همه در دیتابیس ثبت و قابل‌خواندن باشند.

| # | Acceptance Criteria | شرایط پذیرش | Test anchor |
|---|---|---|---|
| AC-06.1 | **Given** a `ready` dataset **When** `POST /training-jobs` (config + status) **Then** the job row is stored, linked to the dataset | ثبت جاب fine-tune با پیکربندی، گره‌خورده به دیتاست | [`test_e2e_07_finetune_and_benchmark_results_are_stored`](../tests/Feature/Api/V1/PipelineE2eTest.php) |
| AC-06.2 | **When** the training completes **Then** `POST /trained-models` stores the final model (`name`, `version`, `status=ready`) linked to the training job | مدل نهایی در دیتابیس ثبت می‌شود | همان تست |
| AC-06.3 | **When** `POST /model-evaluations` (overall score, judge model+prompt) and nested `POST /model-evaluations/{id}/metrics` (per-metric scores) are called **Then** everything is readable back: `data.overall_score`, `data.metric_rows[]` with each metric name/score | نتیجه‌ی بنچمارک (کلی + جزئی) در دیتابیس و از API خوانا | همان تست |
| AC-06.4 | **When** a metric score is out of 0–100 **Then** **422**; metrics of the wrong evaluation are **404** | محدوده‌ی امتیاز و scoping بنچمارک | [`ModelBenchmarkMetricApiTest`](../tests/Feature/Api/V1/ModelBenchmarkMetricApiTest.php) |
| AC-06.5 | **Given** `quality_score` / `quality_rate` fields **When** set outside 0–100 **Then** rejected (project invariant) | محدوده‌ی کیفیت ۰ تا ۱۰۰ (قانون پروژه) | `StoreProcessedOutputRequest` + tests in CRUD matrix |

### F-07 — (آینده) تگ/لیبل چند‌رسانه و منابع فایل خارجی

> **User Story (backlog, not yet built):** تگ/لیبل‌های polymorphic many-to-many برای هر پروژه/فایل/خروجی؛ و منابع فایل خارجی (Google Drive، Nextcloud، YouTube، Aparat) در کنار آپلود مستقیم.

| وضعیت | توضیح |
|---|---|
| ⏳ **در backlog — پیاده‌سازی نشده** | جدول‌ها/میدل‌ورهای مربوطه در skema فعلی نیستند (محدودیت شناخته‌شده §۶). ردیابی فعلی توسط `projects` + `source_types` + `metadata` JSON + `external_ref` انجام می‌شود که بخش عمده‌ی نیاز «منبع‌شناسی» را می‌پوشاند. |

---

## ۲. سناریوهای E2E (هر سناریو = یک تست مستقل)

جدول زیر هر سناریو را **یک‌به‌یک** به متد تست گره می‌زند. اجرای «یکبار برای پذیرش»: `php artisan test --compact --filter=PipelineE2eTest`.

| ID | سناریو | Feature | تست (متد) | چی را ثابت می‌کند (state checks) |
|---|---|---|---|---|
| **E2E-01** | آپلود فایل صوتی با متادیتا + پروژه + نوع منبع → ثبت، دیده‌شدن، دانلود | F-01 | [`test_e2e_01_upload_registers_file_with_metadata_project_and_source_type`](../tests/Feature/Api/V1/PipelineE2eTest.php) | 201 با `file_type=mp3`، `created_by=کلید`، `metadata.*`، `project.name`، `source_type.code`؛ باینری روی دیسک؛ download با `Content-Disposition` |
| **E2E-02** | هر هفت نوع واقعی (docx/pdf/xlsx/txt/mp4/mp3/wav) آپلود و ثبت می‌شوند | F-01 | [`test_e2e_02_all_seven_file_types_upload_and_register`](../tests/Feature/Api/V1/PipelineE2eTest.php) | ۷× 201 با `file_type` درست؛ `GET /files?per_page=20` → ۷ سطر با checksum |
| **E2E-03** | اسکیمای متادیتا: key مجاز ناقص/غلط → 422؛ صحیح → 201 و قابل‌جست‌وجو با کلید | F-02 | [`test_e2e_03_metadata_schema_enforced_on_upload_and_queryable`](../tests/Feature/Api/V1/PipelineE2eTest.php) | 422×2 با پیام کلید؛ 201؛ `?metadata[speaker]=محدث ثانی` → دقیقاً همان فایل |
| **E2E-04** | جاب دستی + خودکار: فرستنده/زمان/مدل/اکشن/منبع/خودکار، همه ذخیره و فیلترپذیر | F-03 | [`test_e2e_04_job_tracks_origin_model_action_and_automation`](../tests/Feature/Api/V1/PipelineE2eTest.php) | 201 با `status=queued`؛ `?source=scheduler`→۱؛ `?is_automatic=0`→۱؛ show با `model.id`/`action.id`/`queued_at`؛ اسرت مستقیم روی DB |
| **E2E-05** | زنجیره‌ی صوت: زیرنویس → اصلاح (مدل/پرامپت دوم) → خلاصه (سوم)؛ JSON نتیجه در DB؛ همه ردیابی‌پذیر به یک فایل | F-04 | [`test_e2e_05_audio_chain_results_are_stored_and_traceable`](../tests/Feature/Api/V1/PipelineE2eTest.php) | 3 خروجی با 3 نوع؛ `?source_file_id=`→۳؛ `?output_type_id=`→۱ برای هر مرحله؛ زنجیره‌ی `parent_job_id` |
| **E2E-06** | دیتاست از خروجی‌های فیلترشده ساخته می‌شود؛ v1→v2 با `previous_dataset_id` | F-05 | [`test_e2e_06_dataset_built_from_filtered_outputs_and_versioned`](../tests/Feature/Api/V1/PipelineE2eTest.php) | انتخاب دقیق ۲ آیتم (مقایسه idها)؛ `dataset-items?dataset_id`→۲؛ v2 با `previous_dataset.id` = v1 |
| **E2E-07** | Fine-tune: training job → مدل نهایی → evaluation کلی + ۲ متریک بنچمارک، همه از API خوانا | F-06 | [`test_e2e_07_finetune_and_benchmark_results_are_stored`](../tests/Feature/Api/V1/PipelineE2eTest.php) | `overall_score=82.50`، `metric_rows`→۲ (accuracy/fluency)، `trained_model.status=ready` |
| **E2E-08** | رویدادهای یکپارچه‌سازی: webhook روی فایل و جاب ثبت می‌شود؛ تاریخچه‌ی هر reference قابل بازپخش | F-03 | [`test_e2e_08_integration_events_record_and_replay_history`](../tests/Feature/Api/V1/PipelineE2eTest.php) | 3× 201 webhook؛ history فایل→۲، جاب→۱ |
| **E2E-09** | چرخه‌ی batch: تخمین → ۲ جاب موفق + ۱ ناموفق → close-out → واقعی‌ها جمع‌بندی می‌شوند | F-03 | [`test_e2e_09_batch_close_out_aggregates_actuals`](../tests/Feature/Api/V1/PipelineE2eTest.php) | `status=partially_failed`، `actual_total_tokens=2000`، `actual_duration_seconds=75`، `completed_at` ست |

**معماری تست:** هر تست یک دیتابیس تازه (RefreshDatabase از روی مایگریشن‌های واقعی) + یک API key واقعی دارد؛ فایل‌ها روی `Storage::fake('local')` می‌نشینند؛ هیچ تستی به تست دیگری وابسته نیست (هر سناریو *تک‌تک* قابل اجراست). در سناریوهای «نتیجه‌ی جاب» (E2E-05/09) worker خارجی هنوز در این ریپازیتوری نیست (محدودیت §۶)؛ حالت «worker کامل‌کرده است» به‌صورت قطعی و تکرارپذیر با factory شبیه‌سازی می‌شود — لایه‌ی API برای خواندن/فیلتر کردن این نتایج واقعی است.

---

## ۳. الزامات تست‌نویسی (به‌ازای هر لایه)

| لایه | الزام | پوشش فعلی |
|---|---|---|
| **L2 — CRUD** | هر resource: ۵ تست (list/create/show/update/delete) از روی [`CrudApiTestCase`](../tests/Feature/Api/V1/CrudApiTestCase.php) با payload واقعی کامل | **29 resource × 5 = 145 تست** |
| **L1 — احراز هویت** | ۴۰۱ بدون کلید/کلید غلط/revoked؛ `created_by` خودکار؛ ثبت `last_used_at` | [`ApiAuthenticationTest`](../tests/Feature/Api/V1/ApiAuthenticationTest.php) (6) |
| **L3a — آپلود/دانلود** | happy path + matrix منفی (413/415/422/410) + schema-on-upload | [`FileUploadTest`](../tests/Feature/Api/V1/FileUploadTest.php) (18) |
| **L3b — متادیتا** | فیلتر + اعتبارسنجی روی JSON endpoints | [`MetadataFilesApiTest`](../tests/Feature/Api/V1/MetadataFilesApiTest.php) (6) |
| **L3c — رفتار خاص** | فیلتر status روی batch‌ها، close-out، scoping metrics، ارجاع‌های webhook | [`JobBatchApiTest`](../tests/Feature/Api/V1/JobBatchApiTest.php) (4) + [`ModelBenchmarkMetricApiTest`](../tests/Feature/Api/V1/ModelBenchmarkMetricApiTest.php) (2) + [`IntegrationEventApiTest`](../tests/Feature/Api/V1/IntegrationEventApiTest.php) (6) |
| **L4 — E2E** | هر سناریوی کاربر = یک تست مستقل در [`PipelineE2eTest`](../tests/Feature/Api/V1/PipelineE2eTest.php) (9 تست، 127 assertion) | §۲ بالا |
| **مجموع** | **198 تست / 652 assertion** | `php artisan test --compact` |

**قواعد نوشتن تست جدید (برای تیم):**
1. Feature test بر Unit test ترجیح دارد؛ سناریوی جدیدی که کاربر را درگیر می‌کند **باید** در `PipelineE2eTest` یک متد `test_e2e_NN_*` بگیرد و در جدول §۲ ثبت شود.
2. آسرت «حالت دیتابیس» (`assertDatabaseHas` / `Model::find`) فقط برای state است؛ رفتار از طریق API black-box امتحان شود.
3. هر test مستقل است: setup با factory، بدون وابستگی به test قبلی؛ `Storage::fake` برای هر تستی که باینری می‌گذارد.
4. برای آسرت ستون‌های `decimal:2` شکل رشته‌ای را استفاده کنید (مثلاً `'82.50'`).
5. تست‌های جدید را با `php artisan test --compact --filter=<Class>` و در انتها کل suite سبز کنید + `vendor/bin/pint --dirty --format agent`.

## ۴. Seeder (داده‌های مرجع برای اجرای صفر‌به‌سناریو)

`php artisan migrate:fresh && php artisan db:seed` (دستور کامل `DatabaseSeeder`):

| جداول سیدشده | مقدار |
|---|---|
| `source_types` | 8 (audio/video/pdf/docx/xlsx/pptx/image/text) |
| `projects` | 2 (مجمع حکمت خراسانی، سمائه) |
| `output_types` | 5 (lecture-transcript, slides-extracted, quiz-questions, summary, cleaned-text) |
| `automation_actions` | 6 (extract-transcript, extract-slides, generate-quiz, summarize, **refine-text**، clean-text) |
| `models` | 4 (deepseek-chat-v3, gpt-4o, gpt-4o-mini, bge-m3) |
| `prompts` | 6 (transcript-extractor, quiz-generator, cleaning, **refiner**، **summarizer**، judge) |
| `service_registry` | 3 (LLM Gateway, Vector Store, Training Service) |
| `automation_flows` | 2 (PDF به آزمون، صدای سخنرانی به متن) |
| `users` | 1 |

سیدر **idempotent** است (`updateOrCreate` با natural key) — دوبار اجرا هم امن است. اکشن‌های `refine-text` و پرامپت‌های `refiner`/`summarizer` دقیقاً برای زنجیره‌ی E2E-05 (زیرنویس → اصلاح → خلاصه) اضافه شده‌اند.

## ۵. Definition of Done (چک‌لیست تحویل)

- [x] **APIs**: تمام ۳۱ endpoint group (29 resource + upload/download + close-out + webhooks + history + metrics) تست شده — 198 تست سبز.
- [x] **سناریوهای E2E**: هر ۷ خواسته‌ی کاربری به ۹ سناریوی خودکار (E2E-01…09) تبدیل شده، هرکدام یک متد مستقل.
- [x] **Seeder**: `db:seed` داده‌های مرجع کامل می‌سازد (جدول §۴)؛ اجرای صفر‌به‌سناریو فقط `migrate:fresh && db:seed` + یک API key.
- [x] **مایگریشن**: ۲۴+ جدول + مایگریشن‌های تکمیلی (`source`/`is_automatic` روی `automation_jobs`) — همه از روی فایل‌های واقعی در تست‌ها migrate می‌شوند.
- [x] **ردیابی‌پذیری**: هر `processed_outputs` = `source_files × automation_actions × models × prompts` (AC-04.1، E2E-05).
- [x] **مستندات API**: reference در [`README.md`](../README.md) (بخش API Reference) + این سند + [`e2e-test-coverage.md`](e2e-test-coverage.md).
- [ ] **CI/CD**: در این ریپازیتوری کانفیغ CI نیست — برای CI کافی است دو دستور بالا در یک job اجرا شوند: `composer run test` / `vendor/bin/pint --test --format agent`. (نیازمند راه‌اندازی خارج از اسکوپ فعلی.)
- [ ] **Coverage**: `vendor/bin/phpunit --coverage-clover` (pcov/xdebug در CI) — آستانه‌ی پیشنهادی 80٪.

## ۶. محدودیت‌های شناخته‌شده (Known limitations)

| # | مورد | وضعیت |
|---|---|---|
| 1 | **worker/پردازش جاب** در این ریپازیتوری نیست (`config/ai-factory.php` → `processor=sim` برای فاز بعدی ذخیره شده). نتیجه‌ی «جاب کامل‌شده» در E2E-05/09 با factory شبیه‌سازی می‌شود؛ لایه‌ی ثبت/فیلتر/ردیابی کاملاً واقعی است. | شناخته‌شده — فاز بعدی |
| 2 | **`model_evaluations.baseline_model_id`** در مدل/میدل‌ور به‌صورت foreign key سختی به `models` referans نمی‌شود (در طرح اولیه dangling بود). در `ModelEvaluation` موجود است ولی بدون constraint؛ test coverage ندارد. | شناخته‌شده — برای فاز بعدی |
| 3 | **تگ/لیبل polymorphic** و **منابع فایل خارجی** (Drive/Nextcloud/YouTube/Aparat) در F-07 هستند — schema فعلی دارد را با `projects` + `source_types` + `metadata` + `external_ref` می‌کند. | Backlog (F-07) |

## ۷. اسناد مرتبط

| سند | رابطه |
|---|---|
| [`e2e-test-coverage.md`](e2e-test-coverage.md) | دفترچه‌ی پوشش تست لایه‌به‌لایه (AUTH/CRUD/UPLOAD/META/E2E) با IDهای سکانس |
| [`acceptance-delivery-plan.md`](acceptance-delivery-plan.md) | قرارداد تحویل فازبه‌فاز (EP-01…14) |
| [`core-ai-factory-schema-api.md`](core-ai-factory-schema-api.md) | اسکرما + API reference |
| [`.ai/rules/*.md`](../.ai/rules/) | قوانین دائمی پروژه (ردیابی‌پذیری، عدم‌تغییرپذیری، محدوده‌ی کیفیت ۰–۱۰۰) |
