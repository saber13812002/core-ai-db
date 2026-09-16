# طراحی اسکیمای دیتابیس مرکزی «کارخانه هوش مصنوعی مجمع حکمت»

با سلام و احترام. بر اساس دو سند ارائه‌شده (فایل صوتی پیاده‌شده و سند جامع نیازمندی‌ها)، معماری زیر را به عنوان یک **کارشناس ارشد طراحی پایگاه داده** پیشنهاد می‌دهم. طراحی به گونه‌ای است که:

- **ضرب دکارتی** (نوع فایل × اکشن × مدل × پرامپت) را در هسته پشتیبانی کند.
- **One-to-Many** از فایل اصلی به انواع خروجی‌ها و از هر خروجی به نسخه‌های تمیزشده و بنچ‌مارک‌ها برقرار باشد.
- **Traceability** تا سطح ثانیه/صفحه حفظ شود.
- چرخه ۷ مرحله‌ای (ذخیره‌سازی → اتوماسیون → بنچ‌مارک → دیتاست → ترین → ارزیابی مدل → ریلیز) پوشش داده شود.
- سرویس‌های خارجی (Fine-tune، OCR، Benchmark) از طریق **Gateway/Orchestrator** با شناسه فایل اصلی (که دست ماست) ارتباط بگیرند.

---

## ۱. معماری کلان و اصول طراحی

```
┌─────────────────────────────────────────────────────────────────────┐
│                    لایه اپلیکیشن (UI/UX)                            │
│         (شبیه Mahd/Fakoor - ساده، تمیز، بدون آموزش)                │
└──────────────────────────────┬──────────────────────────────────────┘
                               │
┌──────────────────────────────▼──────────────────────────────────────┐
│              Gateway / Orchestrator (API Layer)                     │
│   - مسیریابی به سرویس‌های داخلی و خارجی                            │
│   - احراز هویت، Rate Limiting، Logging                              │
└──┬────────────┬────────────┬────────────┬────────────┬─────────────┘
   │            │            │            │            │
   ▼            ▼            ▼            ▼            ▼
┌──────┐  ┌──────────┐  ┌──────────┐  ┌──────────┐  ┌──────────────┐
│ DB   │  │ OCR/STT  │  │ Dify/n8n │  │ Fine-tune│  │ Benchmark    │
│Service│  │ Services │  │ Flows    │  │ Service  │  │ Service      │
│(اینجا)│  │          │  │          │  │          │  │              │
└──────┘  └──────────┘  └──────────┘  └──────────┘  └──────────────┘
   │
   ▼
┌─────────────────────────────────────────────────────────────────────┐
│  PostgreSQL (Core)  +  ChromaDB/Vector DB (Collections)            │
│  +  Object Storage (S3/MinIO for raw files & outputs)              │
└─────────────────────────────────────────────────────────────────────┘
```

**اصول حاکم:**
1. **Immutable Data**: هیچ داده‌ای Overwrite نمی‌شود؛ فقط Versioning و Soft Delete.
2. **Traceability**: هر خروجی باید `source_file_id` و در صورت نیاز `chunk_ref` (ثانیه/صفحه) داشته باشد.
3. **Separation of Concerns**: سرویس DB فقط ۵ مسئولیت اصلی دارد (شناسنامه، تولیدات، پرامپت‌ها، تمیزشده‌ها، بنچ‌مارک)؛ سایر مراحل (دیتاست، ترین، ریلیز) به صورت «متادیتای هماهنگی» در همین DB نگه داشته می‌شوند ولی اجرایشان در سرویس‌های خارجی است.
4. **Cartesian Logic**: ترکیب (SourceType × Action × Model × Prompt) در قالب `automation_jobs` و `processed_outputs` قابل رهگیری است.

---

## ۲. نمودار ERD مفهومی (سطح بالا)

```
┌────────────────┐
│  source_files  │◄──────────────────────────────────────────┐
│  (شناسنامه)     │                                          │
└───────┬────────┘                                          │
        │ 1                                                  │
        │                                                    │
        │ N                                                  │
┌───────▼────────────┐    ┌──────────────────┐              │
│ automation_jobs    │───►│ processed_outputs│              │
│ (صف اجرا)          │    │ (تولیدات خام)     │              │
└───────┬────────────┘    └────────┬─────────┘              │
        │                          │ 1                       │
        │                          │                         │
        │                          │ N                       │
        │                 ┌────────▼─────────┐               │
        │                 │ cleaned_outputs  │               │
        │                 │ (تولیدات تمیز)    │               │
        │                 └────────┬─────────┘               │
        │                          │                         │
        │                          │                         │
        │                 ┌────────▼─────────────┐           │
        │                 │ human_ground_truth   │           │
        │                 │ (داده طلایی انسانی)   │           │
        │                 └────────┬─────────────┘           │
        │                          │                         │
        │                 ┌────────▼─────────────┐           │
        └────────────────►│ benchmark_results    │           │
                          │ (نتایج بنچ‌مارک)      │           │
                          └────────┬─────────────┘           │
                                   │                         │
                          ┌────────▼─────────────┐           │
                          │ datasets / items     │           │
                          │ (مرحله ۴)             │           │
                          └────────┬─────────────┘           │
                                   │                         │
                          ┌────────▼─────────────┐           │
                          │ training_jobs        │           │
                          │ (مرحله ۵ - خارجی)     │           │
                          └────────┬─────────────┘           │
                                   │                         │
                          ┌────────▼─────────────┐           │
                          │ trained_models       │           │
                          │ (مرحله ۶)             │           │
                          └────────┬─────────────┘           │
                                   │                         │
                          ┌────────▼─────────────┐           │
                          │ model_evaluations    │           │
                          │ (مرحله ۶ - بنچ‌مارک)  │           │
                          └────────┬─────────────┘           │
                                   │                         │
                          ┌────────▼─────────────┐           │
                          │ model_releases       │           │
                          │ (مرحله ۷ - ریلیز)     │           │
                          └──────────────────────┘           │
                                                             │
┌──────────────────┐   ┌──────────────────┐   ┌─────────────▼──────┐
│ output_types     │   │ automation_actions│   │ prompts            │
│ (انواع خروجی)     │   │ (انواع اکشن)      │   │ (مستر پرامپت‌ها)    │
└──────────────────┘   └──────────────────┘   └────────────────────┘
        ▲                       ▲                       ▲
        │                       │                       │
        └───────────────────────┴───────────────────────┘
                                │
                    ┌───────────▼───────────┐
                    │ models / service_registry│
                    │ (مدل‌ها و سرویس‌ها)      │
                    └───────────────────────┘
```

---

## ۳. جداول تفصیلی (DDL کامل PostgreSQL)

### ۳.۱. لایه ۱ — شناسنامه فایل (Source File Identity)

```sql
-- ============================================================
-- CORE: FILE IDENTITY (شناسنامه فایل)
-- ============================================================
CREATE TABLE source_files (
    id                  UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    -- شناسه اصلی که به همه سرویس‌ها ارسال می‌شود
    external_ref        VARCHAR(100) UNIQUE,        -- شناسه انسانی/قابل خواندن (مثل MH-1403-001)
    
    -- نوع و مشخصات فایل
    file_type           VARCHAR(50) NOT NULL,       -- voice, video, pdf, word, excel, ppt, image, text
    original_filename   VARCHAR(500),
    storage_path        TEXT NOT NULL,              -- مسیر در Object Storage
    mime_type           VARCHAR(100),
    file_size_bytes     BIGINT,
    checksum_sha256     VARCHAR(64),                -- برای تشخیص تغییر
    duration_seconds    INTEGER,                    -- برای صوت/ویدیو
    page_count          INTEGER,                    -- برای اسناد
    language            VARCHAR(10) DEFAULT 'fa',
    
    -- متادیتای پویا (JSONB برای انعطاف)
    metadata            JSONB DEFAULT '{}',
    -- مثال: {"professor":"...", "topic":"...", "date":"...", "project":"...", 
    --        "season":1, "session":3, "occasion":"...", "location":"..."}
    
    -- تگ تایید انسانی (برای استفاده در بنچ‌مارک)
    human_approved      BOOLEAN DEFAULT FALSE,
    human_approved_by   UUID,
    human_approved_at   TIMESTAMPTZ,
    human_approval_note TEXT,
    
    -- وضعیت پردازش
    processing_status   VARCHAR(50) DEFAULT 'pending',
    -- pending, ocr_done, asr_done, chunked, processed, failed, archived
    
    -- کنترل نسخه و حذف نرم
    version_number      INTEGER DEFAULT 1,
    superseded_by_id    UUID REFERENCES source_files(id),
    is_latest           BOOLEAN DEFAULT TRUE,
    deleted_at          TIMESTAMPTZ,                -- Soft Delete
    
    created_by          UUID,
    created_at          TIMESTAMPTZ DEFAULT NOW(),
    updated_at          TIMESTAMPTZ DEFAULT NOW()
);

CREATE INDEX idx_sf_type          ON source_files(file_type);
CREATE INDEX idx_sf_status        ON source_files(processing_status);
CREATE INDEX idx_sf_approved      ON source_files(human_approved) WHERE human_approved = TRUE;
CREATE INDEX idx_sf_metadata_gin  ON source_files USING GIN(metadata);
CREATE INDEX idx_sf_latest        ON source_files(is_latest) WHERE is_latest = TRUE;
```

### ۳.۲. لایه ۲ — انواع خروجی، اکشن‌ها، مدل‌ها، فلوها

```sql
-- ============================================================
-- OUTPUT TYPES (انواع خروجی)
-- ============================================================
CREATE TABLE output_types (
    id              SERIAL PRIMARY KEY,
    code            VARCHAR(80) UNIQUE NOT NULL,    -- full_text, subtitle_srt, summary, topic_list, qa_pairs, ...
    name_fa         VARCHAR(200) NOT NULL,
    description     TEXT,
    output_format   VARCHAR(50),                    -- json, srt, text, markdown, html
    json_schema     JSONB,                          -- JSON Schema برای اعتبارسنجی خروجی نهایی
    supports_chunk  BOOLEAN DEFAULT FALSE,          -- آیا خروجی قابل تکه‌تکه شدن است؟
    is_active       BOOLEAN DEFAULT TRUE,
    created_at      TIMESTAMPTZ DEFAULT NOW()
);

-- ============================================================
-- AUTOMATION ACTIONS (انواع اکشن روی فایل)
-- ============================================================
CREATE TABLE automation_actions (
    id                  SERIAL PRIMARY KEY,
    code                VARCHAR(80) UNIQUE NOT NULL, -- stt, ocr, summarize, translate, topic_extract, chunking, ...
    name_fa             VARCHAR(200) NOT NULL,
    description         TEXT,
    action_category     VARCHAR(50),                 -- transformation, extraction, generation, evaluation
    input_file_types    TEXT[] NOT NULL,             -- آرایه انواع فایل ورودی مجاز
    output_type_id      INTEGER REFERENCES output_types(id),
    requires_prompt     BOOLEAN DEFAULT FALSE,
    is_batchable        BOOLEAN DEFAULT TRUE,
    is_active           BOOLEAN DEFAULT TRUE,
    created_at          TIMESTAMPTZ DEFAULT NOW()
);

CREATE INDEX idx_actions_input_types ON automation_actions USING GIN(input_file_types);

-- ============================================================
-- PROMPTS (مستر پرامپت‌ها با نسخه‌گذاری Git-like)
-- ============================================================
CREATE TABLE prompts (
    id                  UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    family_id           UUID NOT NULL,              -- گروه همه نسخه‌های یک پرامپت
    name                VARCHAR(300) NOT NULL,
    version             VARCHAR(50) NOT NULL,       -- v1.0, v1.1, v2.0
    content             TEXT NOT NULL,
    content_hash        VARCHAR(64) NOT NULL,       -- SHA-256 برای تشخیص تغییر
    prompt_type         VARCHAR(50) NOT NULL,       -- master, cleaning, benchmark_judge, system, user_template
    purpose             TEXT,
    target_output_type_id INTEGER REFERENCES output_types(id),
    target_action_id    INTEGER REFERENCES automation_actions(id),
    parent_prompt_id    UUID REFERENCES prompts(id),-- درخت نسخه‌ها
    is_active           BOOLEAN DEFAULT TRUE,
    tags                TEXT[],
    metadata            JSONB DEFAULT '{}',
    created_by          UUID,
    created_at          TIMESTAMPTZ DEFAULT NOW(),
    UNIQUE(family_id, version)
);

CREATE INDEX idx_prompts_family ON prompts(family_id);
CREATE INDEX idx_prompts_type   ON prompts(prompt_type);
CREATE INDEX idx_prompts_active ON prompts(is_active) WHERE is_active = TRUE;

-- ============================================================
-- MODELS (مدل‌های هوش مصنوعی)
-- ============================================================
CREATE TABLE models (
    id              SERIAL PRIMARY KEY,
    code            VARCHAR(120) UNIQUE NOT NULL,   -- whisper-v3, claude-3.5-sonnet, gpt-4o, ...
    name_fa         VARCHAR(200),
    provider        VARCHAR(100),                   -- openai, anthropic, local, ...
    model_type      VARCHAR(50),                    -- ASR, LLM, OCR, embedding, vision
    version         VARCHAR(50),
    endpoint        TEXT,
    context_window  INTEGER,                        -- برای LLM
    is_active       BOOLEAN DEFAULT TRUE,
    metadata        JSONB DEFAULT '{}',
    created_at      TIMESTAMPTZ DEFAULT NOW()
);

-- ============================================================
-- AUTOMATION FLOWS (فلوهای Dify/n8n)
-- ============================================================
CREATE TABLE automation_flows (
    id                  UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    name                VARCHAR(300) NOT NULL,
    platform            VARCHAR(50) NOT NULL,       -- dify, n8n, custom
    platform_flow_id    VARCHAR(200),               -- ID در پلتفرم خارجی
    description         TEXT,
    input_file_types    TEXT[],
    output_type_id      INTEGER REFERENCES output_types(id),
    action_id           INTEGER REFERENCES automation_actions(id),
    config              JSONB DEFAULT '{}',
    is_active           BOOLEAN DEFAULT TRUE,
    created_at          TIMESTAMPTZ DEFAULT NOW(),
    updated_at          TIMESTAMPTZ DEFAULT NOW()
);

-- ============================================================
-- SERVICE REGISTRY (سرویس‌های داخلی و خارجی)
-- ============================================================
CREATE TABLE service_registry (
    id                  SERIAL PRIMARY KEY,
    name                VARCHAR(200) NOT NULL,
    service_type        VARCHAR(50) NOT NULL,       -- OCR, STT, LLM, fine_tuning, benchmark, vector_db, orchestrator
    base_url            TEXT,
    api_key_ref         VARCHAR(200),               -- ارجاع به Secret Manager
    endpoints           JSONB DEFAULT '{}',         -- {health, process, status, cancel, ...}
    is_active           BOOLEAN DEFAULT TRUE,
    health_status       VARCHAR(50) DEFAULT 'unknown',
    last_health_check   TIMESTAMPTZ,
    metadata            JSONB DEFAULT '{}',
    created_at          TIMESTAMPTZ DEFAULT NOW()
);
```

### ۳.۳. لایه ۳ — صف اجرا و ضرب دکارتی (Jobs)

```sql
-- ============================================================
-- AUTOMATION JOBS (صف اجرا - قلب ضرب دکارتی)
-- ============================================================
CREATE TABLE automation_jobs (
    id                      UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    batch_id                UUID,                   -- گروه‌بندی جاب‌های یک اجرا
    
    -- ضرب دکارتی: فایل × اکشن × مدل × پرامپت × فلو
    source_file_id          UUID NOT NULL REFERENCES source_files(id),
    action_id               INTEGER NOT NULL REFERENCES automation_actions(id),
    flow_id                 UUID REFERENCES automation_flows(id),
    model_id                INTEGER REFERENCES models(id),
    prompt_id               UUID REFERENCES prompts(id),          -- پرامپت اصلی یا تمیزکننده
    cleaning_prompt_id      UUID REFERENCES prompts(id),          -- اگر این جاب برای تمیزکردن است
    
    -- ارجاع به نسخه قبلی (برای تکرار در آینده)
    parent_job_id           UUID REFERENCES automation_jobs(id),
    rerun_reason            VARCHAR(50),            -- new_model, new_prompt, manual, scheduled
    rerun_of_job_id         UUID REFERENCES automation_jobs(id),
    
    -- وضعیت اجرا
    status                  VARCHAR(50) DEFAULT 'queued',
    -- queued, running, completed, failed, cancelled, paused, retrying
    priority                INTEGER DEFAULT 5,      -- 1=highest, 10=lowest
    progress_percent        INTEGER DEFAULT 0,
    
    -- تخمین هزینه و زمان
    estimated_cost_usd      NUMERIC(10,4),
    estimated_duration_sec  INTEGER,
    actual_cost_usd         NUMERIC(10,4),
    actual_duration_sec     INTEGER,
    token_count             INTEGER,
    
    -- ارتباط با سرویس خارجی
    service_id              INTEGER REFERENCES service_registry(id),
    external_job_id         VARCHAR(200),           -- ID در سرویس خارجی
    request_payload         JSONB,
    response_payload        JSONB,
    error_message           TEXT,
    retry_count             INTEGER DEFAULT 0,
    max_retries             INTEGER DEFAULT 3,
    
    -- زمان‌ها
    queued_at               TIMESTAMPTZ DEFAULT NOW(),
    started_at              TIMESTAMPTZ,
    completed_at            TIMESTAMPTZ,
    created_at              TIMESTAMPTZ DEFAULT NOW(),
    created_by              UUID
);

CREATE INDEX idx_jobs_source_file  ON automation_jobs(source_file_id);
CREATE INDEX idx_jobs_status       ON automation_jobs(status);
CREATE INDEX idx_jobs_batch        ON automation_jobs(batch_id);
CREATE INDEX idx_jobs_action       ON automation_jobs(action_id);
CREATE INDEX idx_jobs_prompt       ON automation_jobs(prompt_id);
CREATE INDEX idx_jobs_parent       ON automation_jobs(parent_job_id);
```

### ۳.۴. لایه ۴ — تولیدات خام و تمیزشده

```sql
-- ============================================================
-- PROCESSED OUTPUTS (تولیدات خام ماشین)
-- ============================================================
CREATE TABLE processed_outputs (
    id                  UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    job_id              UUID NOT NULL REFERENCES automation_jobs(id),
    source_file_id      UUID NOT NULL REFERENCES source_files(id),
    output_type_id      INTEGER NOT NULL REFERENCES output_types(id),
    action_id           INTEGER NOT NULL REFERENCES automation_actions(id),
    model_id            INTEGER REFERENCES models(id),
    prompt_id           UUID REFERENCES prompts(id),
    
    -- محتوا
    content_text        TEXT,                       -- برای خروجی متنی
    content_json        JSONB,                      -- فایل جیسون نهایی (ساختاریافته)
    storage_path        TEXT,                       -- برای فایل‌های حجیم (SRT, JSON بزرگ)
    content_hash        VARCHAR(64),
    
    -- Traceability در سطح خرد
    chunk_refs          JSONB DEFAULT '[]',         -- [{chunk_index, start_sec, end_sec, page}]
    token_count         INTEGER,
    char_count          INTEGER,
    
    -- کیفیت و تایید انسانی
    quality_score       NUMERIC(5,2),               -- 0-100 از QC خودکار
    is_human_approved   BOOLEAN DEFAULT FALSE,
    human_approved_by   UUID,
    human_approved_at   TIMESTAMPTZ,
    human_notes         TEXT,
    
    -- نسخه‌گذاری
    version_number      INTEGER DEFAULT 1,
    superseded_by_id    UUID REFERENCES processed_outputs(id),
    is_latest           BOOLEAN DEFAULT TRUE,
    
    processing_metadata JSONB DEFAULT '{}',
    created_at          TIMESTAMPTZ DEFAULT NOW()
);

CREATE INDEX idx_po_source_file   ON processed_outputs(source_file_id);
CREATE INDEX idx_po_type          ON processed_outputs(output_type_id);
CREATE INDEX idx_po_action        ON processed_outputs(action_id);
CREATE INDEX idx_po_latest        ON processed_outputs(is_latest) WHERE is_latest = TRUE;
CREATE INDEX idx_po_approved      ON processed_outputs(is_human_approved) WHERE is_human_approved = TRUE;
CREATE INDEX idx_po_content_gin   ON processed_outputs USING GIN(content_json);

-- ============================================================
-- CLEANED OUTPUTS (تولیدات تمیزشده با پرامپت‌های مختلف)
-- ============================================================
CREATE TABLE cleaned_outputs (
    id                  UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    processed_output_id UUID NOT NULL REFERENCES processed_outputs(id),
    source_file_id      UUID NOT NULL REFERENCES source_files(id),
    cleaning_prompt_id  UUID NOT NULL REFERENCES prompts(id),
    model_id            INTEGER REFERENCES models(id),
    
    content_text        TEXT,
    content_json        JSONB,
    storage_path        TEXT,
    content_hash        VARCHAR(64),
    
    -- نوع تمیزکاری
    cleaning_type       VARCHAR(50),                -- normalization, formatting, error_fix, enrichment
    
    quality_score       NUMERIC(5,2),
    is_human_approved   BOOLEAN DEFAULT FALSE,
    human_approved_by   UUID,
    human_approved_at   TIMESTAMPTZ,
    
    version_number      INTEGER DEFAULT 1,
    superseded_by_id    UUID REFERENCES cleaned_outputs(id),
    is_latest           BOOLEAN DEFAULT TRUE,
    
    processing_metadata JSONB DEFAULT '{}',
    created_at          TIMESTAMPTZ DEFAULT NOW()
);

CREATE INDEX idx_co_processed      ON cleaned_outputs(processed_output_id);
CREATE INDEX idx_co_source_file    ON cleaned_outputs(source_file_id);
CREATE INDEX idx_co_prompt         ON cleaned_outputs(cleaning_prompt_id);
CREATE INDEX idx_co_latest         ON cleaned_outputs(is_latest) WHERE is_latest = TRUE;

-- ============================================================
-- HUMAN GROUND TRUTH (داده طلایی انسانی)
-- ============================================================
CREATE TABLE human_ground_truth (
    id                  UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    source_file_id      UUID NOT NULL REFERENCES source_files(id),
    output_type_id      INTEGER NOT NULL REFERENCES output_types(id),
    action_id           INTEGER REFERENCES automation_actions(id),
    
    content_text        TEXT,
    content_json        JSONB,
    storage_path        TEXT,
    
    -- تایید انسانی
    approved_by         UUID NOT NULL,
    approved_at         TIMESTAMPTZ NOT NULL,
    approval_notes      TEXT,
    confidence_level    VARCHAR(20),                -- high, medium, low
    
    -- مبنای تولید (اختیاری)
    based_on_output_id  UUID REFERENCES processed_outputs(id),
    based_on_cleaned_id UUID REFERENCES cleaned_outputs(id),
    
    is_active           BOOLEAN DEFAULT TRUE,
    created_at          TIMESTAMPTZ DEFAULT NOW()
);

CREATE INDEX idx_hgt_source_file   ON human_ground_truth(source_file_id);
CREATE INDEX idx_hgt_type          ON human_ground_truth(output_type_id);
```

### ۳.۵. لایه ۵ — بنچ‌مارک تولیدات (مرحله ۳)

```sql
-- ============================================================
-- BENCHMARK SESSIONS (جلسات بنچ‌مارک)
-- ============================================================
CREATE TABLE benchmark_sessions (
    id                  UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    name                VARCHAR(300) NOT NULL,
    description         TEXT,
    benchmark_type      VARCHAR(50) NOT NULL,
    -- content_quality, model_comparison, rag_quality, prompt_comparison, regression
    source_file_id      UUID REFERENCES source_files(id),   -- null اگر batch باشد
    output_type_id      INTEGER REFERENCES output_types(id),
    action_id           INTEGER REFERENCES automation_actions(id),
    judge_model_id      INTEGER REFERENCES models(id),
    judge_prompt_id     UUID REFERENCES prompts(id),
    status              VARCHAR(50) DEFAULT 'pending',
    total_items         INTEGER,
    processed_items     INTEGER DEFAULT 0,
    started_at          TIMESTAMPTZ,
    completed_at        TIMESTAMPTZ,
    metadata            JSONB DEFAULT '{}',
    created_at          TIMESTAMPTZ DEFAULT NOW()
);

-- ============================================================
-- BENCHMARK RESULTS (نتایج مقایسه‌ای)
-- ============================================================
CREATE TABLE benchmark_results (
    id                      UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    benchmark_session_id    UUID NOT NULL REFERENCES benchmark_sessions(id),
    
    -- آیتم‌های مقایسه‌شده
    candidate_output_id     UUID REFERENCES processed_outputs(id),
    candidate_cleaned_id    UUID REFERENCES cleaned_outputs(id),
    baseline_output_id      UUID REFERENCES processed_outputs(id),   -- نسخه قبلی
    baseline_cleaned_id     UUID REFERENCES cleaned_outputs(id),
    ground_truth_id         UUID REFERENCES human_ground_truth(id),
    
    -- امتیازات
    overall_score           NUMERIC(5,2),           -- 0-100
    quality_rate            NUMERIC(5,2),
    metrics                 JSONB DEFAULT '{}',
    -- {accuracy: 85, completeness: 90, fluency: 88, hallucination: 5, ...}
    
    -- جزئیات مقایسه
    comparison_details      JSONB DEFAULT '{}',
    -- [{paragraph: 1, candidate: "...", baseline: "...", diff_type: "improvement"}]
    is_candidate_better     BOOLEAN,
    improvement_percent     NUMERIC(6,2),
    
    -- داور
    judge_model_id          INTEGER REFERENCES models(id),
    judge_prompt_id         UUID REFERENCES prompts(id),
    judge_raw_response      JSONB,
    
    -- اعتبارسنجی انسانی
    human_validated         BOOLEAN DEFAULT FALSE,
    human_validated_by      UUID,
    human_validated_at      TIMESTAMPTZ,
    
    created_at              TIMESTAMPTZ DEFAULT NOW()
);

CREATE INDEX idx_br_session       ON benchmark_results(benchmark_session_id);
CREATE INDEX idx_br_candidate     ON benchmark_results(candidate_output_id);
CREATE INDEX idx_br_baseline      ON benchmark_results(baseline_output_id);
CREATE INDEX idx_br_better        ON benchmark_results(is_candidate_better);
```

### ۳.۶. لایه ۶ — دیتاست و ترین (مراحل ۴ و ۵)

```sql
-- ============================================================
-- DATASETS (مرحله ۴ - ساخت دیتاست)
-- ============================================================
CREATE TABLE datasets (
    id                      UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    name                    VARCHAR(300) NOT NULL,
    description             TEXT,
    purpose                 VARCHAR(50) NOT NULL,   -- fine_tuning, rag, evaluation
    target_model_type       VARCHAR(50),            -- ASR, LLM, OCR
    target_output_type_id   INTEGER REFERENCES output_types(id),
    
    -- معیارهای انتخاب
    filter_criteria         JSONB DEFAULT '{}',
    -- {source_file_ids: [...], date_range: {...}, output_type: ..., min_quality: 80, human_approved_only: true}
    
    status                  VARCHAR(50) DEFAULT 'building',
    -- building, ready, sent_to_training, archived
    
    total_items             INTEGER DEFAULT 0,
    train_count             INTEGER DEFAULT 0,
    validation_count        INTEGER DEFAULT 0,
    test_count              INTEGER DEFAULT 0,
    
    storage_path            TEXT,                   -- مسیر فایل‌های دیتاست (JSONL, etc.)
    created_by              UUID,
    created_at              TIMESTAMPTZ DEFAULT NOW(),
    updated_at              TIMESTAMPTZ DEFAULT NOW()
);

-- ============================================================
-- DATASET ITEMS (آیتم‌های دیتاست)
-- ============================================================
CREATE TABLE dataset_items (
    id                  UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    dataset_id          UUID NOT NULL REFERENCES datasets(id) ON DELETE CASCADE,
    source_file_id      UUID NOT NULL REFERENCES source_files(id),
    
    -- جفت ورودی/خروجی
    input_output_id     UUID REFERENCES processed_outputs(id),
    input_cleaned_id    UUID REFERENCES cleaned_outputs(id),
    ground_truth_id     UUID REFERENCES human_ground_truth(id),
    
    split               VARCHAR(20) NOT NULL,       -- train, validation, test
    sequence_order      INTEGER,
    metadata            JSONB DEFAULT '{}',
    created_at          TIMESTAMPTZ DEFAULT NOW()
);

CREATE INDEX idx_di_dataset ON dataset_items(dataset_id);
CREATE INDEX idx_di_split   ON dataset_items(dataset_id, split);

-- ============================================================
-- TRAINING JOBS (مرحله ۵ - هماهنگی با سرویس خارجی)
-- ============================================================
CREATE TABLE training_jobs (
    id                  UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    dataset_id          UUID NOT NULL REFERENCES datasets(id),
    service_id          INTEGER REFERENCES service_registry(id),
    external_job_id     VARCHAR(200),
    
    base_model_code     VARCHAR(120),
    training_config     JSONB DEFAULT '{}',         -- hyperparameters
    status              VARCHAR(50) DEFAULT 'queued',
    progress_percent    INTEGER DEFAULT 0,
    
    estimated_cost_usd  NUMERIC(10,4),
    actual_cost_usd     NUMERIC(10,4),
    error_message       TEXT,
    
    started_at          TIMESTAMPTZ,
    completed_at        TIMESTAMPTZ,
    created_at          TIMESTAMPTZ DEFAULT NOW()
);

-- ============================================================
-- TRAINED MODELS (مرحله ۶ - مدل‌های ترین‌شده)
-- ============================================================
CREATE TABLE trained_models (
    id                  UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    training_job_id     UUID REFERENCES training_jobs(id),
    name                VARCHAR(200) NOT NULL,
    version             VARCHAR(50) NOT NULL,
    model_type          VARCHAR(50),
    base_model_code     VARCHAR(120),
    
    storage_path        TEXT,
    service_endpoint    TEXT,
    status              VARCHAR(50) DEFAULT 'ready',
    -- training, ready, benchmarking, approved, production, archived, rejected
    
    metadata            JSONB DEFAULT '{}',
    created_at          TIMESTAMPTZ DEFAULT NOW(),
    UNIQUE(name, version)
);

CREATE INDEX idx_tm_status ON trained_models(status);
```

### ۳.۷. لایه ۷ — ارزیابی مدل و ریلیز (مراحل ۶ و ۷)

```sql
-- ============================================================
-- MODEL EVALUATIONS (مرحله ۶ - بنچ‌مارک مدل ترین‌شده)
-- ============================================================
CREATE TABLE model_evaluations (
    id                      UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    trained_model_id        UUID NOT NULL REFERENCES trained_models(id),
    benchmark_session_id    UUID REFERENCES benchmark_sessions(id),
    
    evaluation_type         VARCHAR(50),            -- automated, human, hybrid
    overall_score           NUMERIC(5,2),
    metrics                 JSONB DEFAULT '{}',
    -- {WER: 0.05, BLEU: 0.85, ROUGE: 0.78, precision: 0.9, recall: 0.88, ...}
    
    -- مقایسه با مدل production
    baseline_model_id       UUID REFERENCES trained_models(id),
    improvement_percent     NUMERIC(6,2),
    is_better_than_baseline BOOLEAN,
    
    judge_model_id          INTEGER REFERENCES models(id),
    judge_prompt_id         UUID REFERENCES prompts(id),
    details                 JSONB DEFAULT '{}',
    
    created_at              TIMESTAMPTZ DEFAULT NOW()
);

-- ============================================================
-- MODEL RELEASES (مرحله ۷ - ریلیز به پروداکشن)
-- ============================================================
CREATE TABLE model_releases (
    id                  UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    trained_model_id    UUID NOT NULL REFERENCES trained_models(id),
    version             VARCHAR(50) NOT NULL,
    
    status              VARCHAR(50) DEFAULT 'draft',
    -- draft, pending_approval, approved, in_production, retired, rejected
    
    approved_by         UUID,
    approved_at         TIMESTAMPTZ,
    rejection_reason    TEXT,
    
    release_notes       TEXT,
    performance_summary JSONB DEFAULT '{}',
    
    deployed_at         TIMESTAMPTZ,
    retired_at          TIMESTAMPTZ,
    created_at          TIMESTAMPTZ DEFAULT NOW()
);

-- ============================================================
-- RELEASE REPORTS (گزارش‌های مرحله ۷)
-- ============================================================
CREATE TABLE release_reports (
    id              UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    release_id      UUID REFERENCES model_releases(id),
    report_type     VARCHAR(50),                    -- summary, detailed, comparison
    content         JSONB NOT NULL,
    storage_path    TEXT,
    generated_at    TIMESTAMPTZ DEFAULT NOW()
);
```

### ۳.۸. جداول پشتیبان (Feedback, Logs, API)

```sql
-- ============================================================
-- FEEDBACK LOGS (بازخورد کاربران)
-- ============================================================
CREATE TABLE feedback_logs (
    id                  BIGSERIAL PRIMARY KEY,
    source_file_id      UUID REFERENCES source_files(id),
    processed_output_id UUID REFERENCES processed_outputs(id),
    cleaned_output_id   UUID REFERENCES cleaned_outputs(id),
    trained_model_id    UUID REFERENCES trained_models(id),
    
    feedback_type       VARCHAR(20) NOT NULL,       -- like, dislike, report, suggestion
    user_id             VARCHAR(200),
    session_id          VARCHAR(200),
    comment             TEXT,
    context             JSONB DEFAULT '{}',         -- query, response, ...
    
    created_at          TIMESTAMPTZ DEFAULT NOW()
);

CREATE INDEX idx_fl_type     ON feedback_logs(feedback_type);
CREATE INDEX idx_fl_output   ON feedback_logs(processed_output_id);
CREATE INDEX idx_fl_file     ON feedback_logs(source_file_id);

-- ============================================================
-- SERVICE CALL LOGS (لاگ تماس با سرویس‌های خارجی)
-- ============================================================
CREATE TABLE service_call_logs (
    id                  BIGSERIAL PRIMARY KEY,
    service_id          INTEGER REFERENCES service_registry(id),
    source_file_id      UUID REFERENCES source_files(id),   -- شناسه ما که به سرویس ارسال شد
    job_id              UUID REFERENCES automation_jobs(id),
    endpoint            VARCHAR(500),
    http_method         VARCHAR(10),
    request_payload     JSONB,
    response_payload    JSONB,
    http_status         INTEGER,
    duration_ms         INTEGER,
    error_message       TEXT,
    created_at          TIMESTAMPTZ DEFAULT NOW()
);

CREATE INDEX idx_scl_source_file ON service_call_logs(source_file_id);
CREATE INDEX idx_scl_created     ON service_call_logs(created_at);
CREATE INDEX idx_scl_service     ON service_call_logs(service_id);
```

---

## ۴. ویوهای کلیدی برای «ضرب دکارتی» و گزارش‌گیری

```sql
-- ============================================================
-- VIEW: ماتریس اجرا (Cartesian Product Matrix)
-- نمایش همه ترکیب‌های ممکن فایل × اکشن × مدل × پرامپت
-- ============================================================
CREATE VIEW v_execution_matrix AS
SELECT
    sf.id                   AS source_file_id,
    sf.external_ref         AS file_ref,
    sf.file_type,
    aa.id                   AS action_id,
    aa.code                 AS action_code,
    m.id                    AS model_id,
    m.code                  AS model_code,
    p.id                    AS prompt_id,
    p.name                  AS prompt_name,
    p.version               AS prompt_version,
    po.id                   AS output_id,
    po.content_hash,
    po.quality_score,
    po.is_latest,
    po.is_human_approved,
    po.created_at           AS output_created_at,
    aj.status               AS job_status,
    aj.started_at,
    aj.completed_at,
    aj.actual_cost_usd
FROM source_files sf
CROSS JOIN LATERAL (
    SELECT * FROM automation_actions aa2
    WHERE sf.file_type = ANY(aa2.input_file_types)
      AND aa2.is_active = TRUE
) aa
LEFT JOIN processed_outputs po
    ON po.source_file_id = sf.id AND po.action_id = aa.id
LEFT JOIN automation_jobs aj ON aj.id = po.job_id
LEFT JOIN models m ON m.id = po.model_id
LEFT JOIN prompts p ON p.id = po.prompt_id
WHERE sf.deleted_at IS NULL;

-- ============================================================
-- VIEW: آخرین نسخه هر تولید
-- ============================================================
CREATE VIEW v_latest_outputs AS
SELECT *
FROM processed_outputs
WHERE is_latest = TRUE AND superseded_by_id IS NULL;

-- ============================================================
-- VIEW: مقایسه بنچ‌مارک با نسخه قبلی
-- ============================================================
CREATE VIEW v_benchmark_comparison AS
SELECT
    br.id                   AS benchmark_result_id,
    bs.name                 AS session_name,
    sf.external_ref         AS file_ref,
    ot.code                 AS output_type,
    br.overall_score,
    br.quality_rate,
    br.improvement_percent,
    br.is_candidate_better,
    br.metrics,
    cm.code                 AS candidate_model,
    bm.code                 AS baseline_model,
    br.created_at
FROM benchmark_results br
JOIN benchmark_sessions bs ON bs.id = br.benchmark_session_id
LEFT JOIN source_files sf ON sf.id = bs.source_file_id
LEFT JOIN output_types ot ON ot.id = bs.output_type_id
LEFT JOIN processed_outputs cpo ON cpo.id = br.candidate_output_id
LEFT JOIN models cm ON cm.id = cpo.model_id
LEFT JOIN processed_outputs bpo ON bpo.id = br.baseline_output_id
LEFT JOIN models bm ON bm.id = bpo.model_id;
```

---

## ۵. جریان‌های کلیدی (Use Cases)

### ۵.۱. جریان «تولید زیرنویس با چند مدل و تمیزکاری»

```
1. کاربر فایل صوتی را آپلود می‌کند → source_files (file_type='voice')
2. سیستم سه جاب در صف قرار می‌دهد:
   - Job1: (source_file, action=stt, model=whisper-v3, prompt=prompt-stt-v1)
   - Job2: (source_file, action=stt, model=whisper-v4, prompt=prompt-stt-v1)
   - Job3: (source_file, action=stt, model=deepseek-asr, prompt=prompt-stt-v1)
3. هر جاب اجرا می‌شود → processed_outputs (content_json = SRT نهایی)
4. کاربر پرامپت تمیزکاری را انتخاب می‌کند:
   - Cleaning1: (processed_output_1, cleaning_prompt=prompt-clean-v2, model=claude-3.5)
   - Cleaning2: (processed_output_1, cleaning_prompt=prompt-clean-v3, model=gpt-4o)
5. cleaned_outputs ایجاد می‌شود
6. بنچ‌مارک با human_ground_truth مقایسه می‌کند
7. بهترین نسخه is_latest=TRUE می‌گیرد
```

### ۵.۲. جریان «تکرار در آینده با مدل جدید»

```
1. کاربر دکمه «Rerun with new model» را می‌زند
2. سیستم جاب‌های جدید با parent_job_id = جاب قبلی ایجاد می‌کند
3. rerun_reason = 'new_model'
4. نسخه قبلی: is_latest=FALSE, superseded_by_id = نسخه جدید
5. نسخه جدید: version_number = قبلی + 1
6. بنچ‌مارک خودکار با baseline_output_id = نسخه قبلی
```

### ۵.۳. جریان «ساخت دیتاست و ترین»

```
1. کاربر فیلتر می‌زند: human_approved=TRUE, quality_score>80, output_type=subtitle
2. datasets ایجاد می‌شود (status='building')
3. dataset_items پر می‌شود (split: 80% train, 10% val, 10% test)
4. datasets.status = 'ready'
5. training_jobs ایجاد می‌شود (service_id = fine-tune service)
6. سرویس خارجی با source_file_id های ما کار می‌کند
7. trained_models ایجاد می‌شود (status='ready')
8. model_evaluations با benchmark service
9. model_releases با status='pending_approval' → 'approved' → 'in_production'
```

---

## ۶. قرارداد API (برای Gateway/Orchestrator)

سرویس DB ما این اندپوینت‌ها را暴露 می‌کند:

| متد | Endpoint | توضیح |
|------|----------|-------|
| POST | `/api/v1/files` | ثبت شناسنامه فایل جدید (برمی‌گرداند `source_file_id`) |
| GET | `/api/v1/files/{id}` | دریافت شناسنامه + متادیتا |
| GET | `/api/v1/files/{id}/status` | وضعیت پردازش (برای سرویس‌های خارجی) |
| POST | `/api/v1/jobs` | ایجاد جاب در صف (ضرب دکارتی) |
| GET | `/api/v1/jobs/{id}/status` | وضعیت جاب |
| POST | `/api/v1/outputs` | ثبت خروجی تولیدشده |
| POST | `/api/v1/cleaned-outputs` | ثبت خروجی تمیزشده |
| POST | `/api/v1/benchmarks` | ایجاد جلسه بنچ‌مارک |
| POST | `/api/v1/benchmarks/{id}/results` | ثبت نتیجه بنچ‌مارک |
| GET | `/api/v1/files/{id}/lineage` | دریافت کل شجره‌نامه فایل |
| POST | `/api/v1/datasets` | ایجاد دیتاست |
| GET | `/api/v1/datasets/{id}/export` | خروجی JSONL برای ترین |
| POST | `/api/v1/training-jobs` | ثبت جاب ترین |
| POST | `/api/v1/releases` | ثبت ریلیز |

**نکته کلیدی:** همه سرویس‌های خارجی موظفند `source_file_id` (UUID ما) را در درخواست‌ها ارسال کنند و در پاسخ‌ها همان را برگردانند. این تضمین‌کننده Traceability است.

---

## ۷. توضیح تصمیمات طراحی

| نیازمندی | راه‌حل در اسکیما |
|-----------|------------------|
| One-to-Many فایل → خروجی‌ها | `source_files` 1 → N `processed_outputs` |
| ضرب دکارتی (نوع فایل × اکشن × مدل × پرامپت) | `automation_jobs` با FK به `source_files`, `automation_actions`, `models`, `prompts` |
| ذخیره فایل JSON نهایی | `processed_outputs.content_json` (JSONB) + `output_types.json_schema` برای اعتبارسنجی |
| Traceability به فایل اصلی | FK `source_file_id` در همه جداول + `chunk_refs` در `processed_outputs` |
| تکرار در آینده | `parent_job_id`, `rerun_reason`, `version_number`, `superseded_by_id` |
| بنچ‌مارک با نسخه قبلی | `benchmark_results.baseline_output_id` + `improvement_percent` |
| تگ تایید انسانی | `source_files.human_approved` + `human_ground_truth` |
| مرحله ۴ (دیتاست) | `datasets` + `dataset_items` |
| مرحله ۵ (ترین خارجی) | `training_jobs` با `service_id` و `external_job_id` |
| مرحله ۶ (ارزیابی مدل) | `model_evaluations` با `metrics` JSONB |
| مرحله ۷ (ریلیز) | `model_releases` + `release_reports` |
| ارتباط با سرویس‌های خارجی | `service_registry` + `service_call_logs` |
| بازخورد کاربران | `feedback_logs` |
| عدم Overwrite | `is_latest`, `superseded_by_id`, `deleted_at` (Soft Delete) |
| تخمین هزینه قبل از اجرا | `automation_jobs.estimated_cost_usd` و `estimated_duration_sec` |

---

## ۸. سوالات تکمیلی برای شفاف‌سازی

۱. **Chunking**: آیا می‌خواهید هر Chunk یک رکورد جدا در `processed_outputs` باشد یا به صورت `chunk_refs` JSONB در همان رکورد ذخیره شود؟ (پیشنهاد من: JSONB برای سادگی، جدول جدا برای کوئری‌های سنگین‌تر)

۲. **Vector DB**: آیا `ChromaDB` خارج از PostgreSQL است یا می‌خواهید `pgvector` استفاده کنید؟ (پیشنهاد: pgvector برای یکپارچگی + ChromaDB برای مقیاس)

۳. **Multi-tenancy**: آیا چند پروژه (مجمع حکمت، پروژه‌های دیگر) روی یک DB مشترک هستند؟ (در این صورت `tenant_id` یا `project_id` اضافه می‌شود)

۴. **زبان خروجی JSON**: آیا `content_json` باید حتماً از یک Schema مشخص تبعیت کند یا آزاد است؟ (پیشنهاد: `output_types.json_schema` اجباری شود)

۵. **مدل‌های Offline**: آیا مدل‌های Offline (DeepSeek محلی) هم در `models` ثبت می‌شوند یا جدول جداگانه دارند؟

۶. **Rate Limiting و صف**: آیا از Celery/RabbitMQ استفاده می‌کنید یا صف داخلی PostgreSQL کافی است؟

۷. **گزارش‌گیری**: آیا نیاز به OLAP جداگانه (مثل ClickHouse) برای گزارش‌های سنگین دارید یا همان PostgreSQL کافی است؟

---

این طراحی، **پایه‌ای مقیاس‌پذیر، قابل رهگیری و آینده‌نگر** برای «کارخانه کارخانه‌سازی» شما فراهم می‌کند. هر زمان که پاسخ سوالات بالا را دادید، می‌توانم DDL نهایی را با جزئیات بیشتر (ایندکس‌های تخصصی، Partitioning برای جداول حجیم، و Stored Procedureهای پرکاربرد) تکمیل کنم.