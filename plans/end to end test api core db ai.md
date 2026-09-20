صابر طباطبایی یزدی, [9/19/2026 12:33 PM]
بله. با توضیحی که دادی، بهتر است سناریوها را از بیرون سیستم و فقط از طریق Web Service/API طراحی کنیم؛ یعنی در تحویل پروژه، تست‌کننده لازم نباشد بداند داخل سیستم چه سرویس، Queue، Worker، DB یا Workflowای وجود دارد. فقط Input → API → وضعیت → Output → داده قابل مشاهده را بررسی کند.

من ساختار را به سبک Product Backlog → Epic → User Story → Acceptance Criteria → Test Scenario → Definition of Done می‌چینم.


---

1. تعریف سطح تست پذیرش پروژه

جریان اصلی سیستم از دید Black Box:

File Upload
    ↓
Content Registration
    ↓
Job Creation
    ↓
Queue
    ↓
Processing
    ↓
Result Registration
    ↓
Prompt Execution
    ↓
Subtitle
    ↓
Corrected Subtitle
    ↓
Full Text
    ↓
LLM Processed Text
    ↓
Summary
    ↓
Dataset
    ↓
Fine-tuning
    ↓
Fine-tuned Model
    ↓
Benchmark

و در تمام این مراحل باید بتوانیم از API بفهمیم:

چه چیزی؟
چه کسی؟
چه زمانی؟
با چه مدلی؟
با چه Promptی؟
از چه منبعی؟
روی چه فایل/داده‌ای؟
چه Jobای؟
چه نتیجه‌ای؟
با چه Statusای؟


---

2. Definition of Done کل پروژه

قبل از ورود به بک‌لاگ، یک DoD مشترک تعریف کنیم.

Definition of Done – پروژه

یک قابلیت زمانی Done محسوب می‌شود که:

1. API مربوط به آن پیاده‌سازی شده باشد.


2. Request و Response مستند شده باشد.


3. HTTP Status Code مناسب برگردد.


4. Validation ورودی‌ها انجام شود.


5. خطاهای متداول API مشخص و قابل تست باشند.


6. داده ایجادشده در سیستم قابل بازیابی باشد.


7. ارتباط Entityهای مربوطه قابل مشاهده باشد.


8. ID یکتا برای Entity ایجاد شود.


9. تاریخ ایجاد/به‌روزرسانی ثبت شود.


10. Actor/Creator در موارد لازم ثبت شود.


11. وضعیت Processing/Job قابل مشاهده باشد.


12. تست مثبت و منفی نوشته شده باشد.


13. تست Idempotency در APIهای حساس انجام شده باشد.


14. تست Authorization/Authentication انجام شده باشد.


15. API Documentation به‌روز باشد.


16. هیچ اطلاعاتی فقط در Log داخلی قابل مشاهده نباشد و داده مورد نیاز API باید از API قابل دریافت باشد.


17. تست End-to-End سناریوی اصلی با موفقیت اجرا شود.




---

EPIC 01 — مدیریت و ثبت فایل

هدف:

> بتوانیم فایل‌های مختلف را از بیرون سیستم وارد کنیم و یک Content قابل شناسایی داشته باشیم.




---

US-01 — ثبت فایل Word

User Story

به عنوان مصرف‌کننده Web Service، می‌خواهم یک فایل Word ارسال کنم تا فایل در سیستم ثبت و قابل مشاهده باشد.

Acceptance Criteria

API امکان Upload فایل Word داشته باشد.

فایل با موفقیت ثبت شود.

سیستم یک content_id یا file_id یکتا ایجاد کند.

نام فایل ثبت شود.

نوع فایل ثبت شود.

MIME Type ثبت شود.

Size ثبت شود.

تاریخ ثبت ذخیره شود.

منبع ثبت مشخص باشد.

API پاسخ موفق شامل شناسه فایل باشد.

فایل از طریق API قابل بازیابی باشد.

Detail فایل قابل دریافت باشد.


Test

POST /contents
Content-Type: multipart/form-data

file = sample.docx

Expected:

{
  "id": "...",
  "content_type": "document",
  "mime_type": "...",
  "status": "registered"
}


---

US-02 — ثبت PDF

همان سناریو برای:

sample.pdf

Acceptance Criteria:

PDF پذیرفته شود.

Content ثبت شود.

Metadata ثبت شود.

Detail قابل دریافت باشد.

فایل قابل دانلود/دریافت باشد.



---

US-03 — ثبت فایل Text

sample.txt

باید:

ثبت شود.

content_type=text

MIME Type مشخص باشد.

متن قابل دسترسی باشد.



---

US-04 — ثبت فایل صوتی

مثلاً:

sample.mp3
sample.wav

Acceptance:

فایل ثبت شود.

نوع Content برابر Audio باشد.

Metadata صوت در صورت پشتیبانی ثبت شود.

فایل قابل دریافت باشد.



---

US-05 — ثبت فایل Video

مثلاً:

sample.mp4

Acceptance:

Video ثبت شود.

Content ID تولید شود.

Metadata فایل ذخیره شود.

Detail قابل دریافت باشد.



---

EPIC 02 — جزئیات و Metadata

US-06 — مشاهده Detail فایل

API

GET /contents/{content_id}

Acceptance Criteria

Response حداقل شامل:

{
  "id": "...",
  "name": "...",
  "type": "...",
  "mime_type": "...",
  "size": 123456,
  "created_at": "...",
  "source": "...",
  "status": "..."
}

باشد.


---

US-07 — ثبت Metadata به صورت Key/Value

کاربر باید بتواند Metadata دلخواه اضافه کند.

مثلاً:

{
  "speaker": "Ali",
  "language": "fa",
  "topic": "Islamic Theology",
  "event": "Seminar",
  "year": 1405
}

Acceptance Criteria

Metadata به Content متصل شود.

Key تکراری طبق Rule سیستم مدیریت شود.

Value بتواند String/Number/Boolean/Object باشد، در صورت پشتیبانی.

Metadata بعداً قابل Query باشد.

صابر طباطبایی یزدی, [9/19/2026 12:33 PM]
Metadata از API قابل مشاهده باشد.



---

US-08 — Metadata Schema

سیستم باید امکان تعریف Schema داشته باشد.

مثلاً:

{
  "speaker": {
    "type": "string",
    "required": true
  },
  "language": {
    "type": "string",
    "required": true
  },
  "year": {
    "type": "integer",
    "required": false
  }
}

Acceptance Criteria

اگر Schema تعریف شده:

فیلد required → باید ارسال شود
type → باید معتبر باشد
field اضافه → طبق Rule سیستم رد یا پذیرفته شود

تست منفی

{
  "speaker": 123
}

باید Validation Error دریافت شود.


---

EPIC 03 — ثبت Job

اینجا وارد قلب سیستم می‌شویم.

US-09 — ایجاد Job

کاربر باید بتواند بر اساس Content یک Job ایجاد کند.

مثلاً:

{
  "content_id": "CNT-001",
  "action_type": "transcription",
  "model": "whisper",
  "source": "api",
  "automatic": false
}


---

Acceptance Criteria

بعد از ایجاد Job:

Job ID ایجاد شود.

Content ID مشخص باشد.

Action Type ثبت شود.

Model ثبت شود.

Creator مشخص باشد.

Creation Date ثبت شود.

Source مشخص باشد.

Automatic/Manual مشخص باشد.

Status اولیه مشخص باشد.


مثلاً:

{
  "job_id": "JOB-001",
  "status": "queued",
  "content_id": "CNT-001",
  "action_type": "transcription",
  "model": "whisper",
  "created_by": "user-001",
  "source": "api",
  "automatic": false
}


---

US-10 — مشاهده وضعیت Job

GET /jobs/{job_id}

سیستم باید وضعیت‌هایی شبیه این داشته باشد:

created
queued
processing
completed
failed
cancelled

Acceptance Criteria

تغییر وضعیت Job باید از API قابل مشاهده باشد.


---

US-11 — Job History

برای هر Job باید بدانیم:

Created
Queued
Started
Processing
Completed / Failed

و در صورت پشتیبانی:

started_at
completed_at
duration
retry_count
error

قابل مشاهده باشد.


---

EPIC 04 — Queue و Processing

US-12 — قرار گرفتن Job در Queue

Acceptance Criteria

بعد از ایجاد Job:

Job Created
      ↓
Job Queued

و API باید بتواند این وضعیت را نشان دهد.


---

US-13 — اجرای Job

سیستم باید Job را پردازش کند.

تست Black Box فقط این را بررسی می‌کند:

POST Job
↓
GET Job
↓
status = processing
↓
GET Job
↓
status = completed


---

US-14 — خطای Job

یک فایل یا ورودی عمداً نامعتبر ارسال شود.

Expected:

{
  "status": "failed",
  "error": {
    "code": "...",
    "message": "..."
  }
}


---

EPIC 05 — ثبت Result

US-15 — ذخیره Result به صورت JSON

پس از Completion باید Result ثبت شود.

مثلاً:

{
  "job_id": "JOB-001",
  "original_id": "CNT-001",
  "original_type": "audio",
  "action_type": "transcription",
  "result_type": "subtitle",
  "result": {
    "text": "...",
    "segments": []
  }
}

Acceptance Criteria

Result باید حداقل مشخص کند:

Original ID
Original Type
Action Type
Job ID
Result Type
Result Data
Model
Prompt
Created At


---

EPIC 06 — Prompt Management

این بخش خیلی مهم است و پیشنهاد می‌کنم به عنوان یک Epic مستقل در تحویل پروژه قرار بگیرد.

US-16 — ایجاد Prompt

POST /prompts

مثلاً:

{
  "name": "Subtitle Correction",
  "action_type": "subtitle_correction",
  "content_type": "subtitle",
  "model": "qwen",
  "prompt": "..."
}


---

US-17 — CRUD Prompt

باید امکان:

Create
Read
Update
Delete
List

وجود داشته باشد.


---

US-18 — ارتباط Prompt با Action Type

یک Prompt باید بتواند مشخص کند:

برای چه Action؟
برای چه Content Type؟
برای چه Model؟
برای چه Version؟

مثلاً:

Action:
subtitle_correction

Content:
subtitle

Model:
Qwen

Version:
v3


---

EPIC 07 — تولید Subtitle

US-19 — Audio → Subtitle

سناریوی اصلی:

Upload Audio
     ↓
Create Transcription Job
     ↓
Queue
     ↓
Whisper
     ↓
Subtitle

Acceptance Criteria

برای فایل صوتی:

Job ایجاد شود.

Job پردازش شود.

Subtitle ایجاد شود.

Subtitle به Audio اصلی مرتبط باشد.

Result قابل دریافت باشد.



---

US-20 — Subtitle Correction

Subtitle
   ↓
Correction Job
   ↓
Prompt
   ↓
LLM
   ↓
Corrected Subtitle

باید بتوانیم بفهمیم:

original_subtitle_id
prompt_id
model_id
job_id
corrected_subtitle_id


---

US-21 — چند نوع Subtitle

سیستم باید امکان نگهداری چند خروجی را داشته باشد.

مثلاً:

RAW_SUBTITLE
CORRECTED_SUBTITLE
FINAL_SUBTITLE

و هر کدام Parent مشخص داشته باشند.

مثلاً:

Audio
 │
 └── Raw Subtitle
       │
       └── Corrected Subtitle
             │
             └── Final Subtitle


---

صابر طباطبایی یزدی, [9/19/2026 12:33 PM]
EPIC 08 — تولید Full Text

US-22 — Subtitle → Full Text

Corrected Subtitle
        ↓
Full Text Job
        ↓
Prompt
        ↓
LLM
        ↓
Full Text

Acceptance Criteria

Full Text باید به Subtitle اصلی قابل ردیابی باشد.


---

US-23 — Full Text با چند LLM

مثلاً:

Full Text
   ├── Qwen + Prompt A
   ├── Qwen + Prompt B
   ├── Model X + Prompt C
   └── Model Y + Prompt D

هر Result باید مشخص کند:

model
model_version
prompt
prompt_version
job_id
input_id
output_id


---

EPIC 09 — Summary

US-24 — تولید خلاصه

Full Text
    ↓
Summary Job
    ↓
Prompt
    ↓
LLM
    ↓
Summary

Acceptance Criteria

خلاصه باید به متن ورودی قابل Trace باشد.


---

US-25 — Summary با Model/Prompt مختلف

مثلاً:

Qwen + Prompt A
Qwen + Prompt B
Model X + Prompt A
Model Y + Prompt C

و خروجی‌ها نباید روی یکدیگر overwrite شوند.


---

EPIC 10 — Dataset

اینجا سیستم وارد مرحله Data Pipeline می‌شود.

US-26 — ایجاد Dataset

POST /datasets

مثلاً:

{
  "name": "Subtitle Dataset 1405",
  "description": "...",
  "source_type": "subtitle",
  "language": "fa"
}


---

US-27 — اضافه کردن Data به Dataset

مثلاً:

Subtitle
Corrected Subtitle
Metadata
Source

باید بتوانند Dataset Item تشکیل دهند.


---

US-28 — ساخت Dataset از Jobهای قبلی

این سناریو بسیار مهم است:

Audio Files
 ↓
Jobs
 ↓
Subtitles
 ↓
Corrected Subtitles
 ↓
Dataset

Acceptance:

سیستم باید بتواند مشخص کند هر Dataset Item از چه Original Content و چه Resultای ساخته شده است.


---

EPIC 11 — Fine-Tuning

US-29 — ایجاد Fine-Tuning Job

{
  "dataset_id": "DATASET-001",
  "base_model": "MODEL-A",
  "parameters": {
    "epochs": 3
  }
}


---

US-30 — وضعیت Fine-Tuning

created
queued
running
completed
failed


---

US-31 — ثبت Model نهایی

بعد از Fine-Tuning:

{
  "model_id": "...",
  "base_model": "...",
  "dataset_id": "...",
  "training_job_id": "...",
  "created_at": "...",
  "status": "ready"
}


---

EPIC 12 — Benchmark

US-32 — ایجاد Benchmark

{
  "model_id": "...",
  "dataset_id": "...",
  "benchmark_type": "...",
  "configuration": {}
}


---

US-33 — اجرای Benchmark

Dataset
   ↓
Benchmark Job
   ↓
Model
   ↓
Evaluation
   ↓
Result


---

US-34 — ثبت نتیجه Benchmark

مثلاً:

{
  "benchmark_id": "BENCH-001",
  "model_id": "MODEL-001",
  "dataset_id": "DATASET-001",
  "metrics": {
    "accuracy": 0.91,
    "wer": 0.08
  }
}


---

EPIC 13 — Traceability

این Epic را حتماً در قرارداد تحویل پروژه بگذارید.

چون چیزی که شما توصیف کردی فقط «پردازش فایل» نیست؛ یک Data Lineage کامل است.

باید بتوانیم از خروجی نهایی به عقب برگردیم:

Benchmark
   ↓
Fine-tuned Model
   ↓
Training Dataset
   ↓
Dataset Item
   ↓
Corrected Subtitle
   ↓
Raw Subtitle
   ↓
Audio
   ↓
Original Upload

و بالعکس:

Audio
 ↓
تمام Jobها
 ↓
تمام Resultها
 ↓
تمام Promptها
 ↓
تمام Modelها
 ↓
Dataset
 ↓
Fine-tuning
 ↓
Benchmark


---

EPIC 14 — سناریوی End-to-End اصلی

این مهم‌ترین Acceptance Test کل پروژه است.

E2E-001

Input

یک فایل صوتی:

sample.mp3

Step 1

Upload:

POST /contents

Expected:

201 Created
content_id

Step 2

مشاهده:

GET /contents/{id}

Expected:

Audio
Metadata
Source
Created By
Created At

Step 3

ایجاد Transcription Job:

POST /jobs

Expected:

job_id
status=queued

Step 4

بررسی Job:

GET /jobs/{id}

Expected:

queued
→ processing
→ completed

Step 5

دریافت Result:

GET /jobs/{id}/result

Expected:

Subtitle

Step 6

Correction Job:

POST /jobs

با:

action_type=subtitle_correction
prompt_id=...
model=...

Step 7

دریافت Corrected Subtitle.

Step 8

Full Text Job.

Step 9

دریافت Full Text.

Step 10

Summary Job.

Step 11

دریافت Summary.

Step 12

ساخت Dataset:

Subtitle
Corrected Subtitle
Full Text
Metadata

Step 13

Fine-tuning:

Dataset
 ↓
FineTune Job
 ↓
FineTuned Model

Step 14

Benchmark:

Model
 +
Benchmark Dataset
 ↓
Benchmark
 ↓
Metrics


---

3. بک‌لاگ پیشنهادی نهایی

اگر بخواهیم آن را واقعاً به شکل Product Backlog وارد Jira / Azure DevOps / Linear کنیم، ساختار اولیه این است:

ID	Epic	Story

صابر طباطبایی یزدی, [9/19/2026 12:33 PM]
EP-01	Content	Upload Word
EP-01	Content	Upload PDF
EP-01	Content	Upload Text
EP-01	Content	Upload Audio
EP-01	Content	Upload Video
EP-02	Metadata	Content Detail
EP-02	Metadata	Key/Value Metadata
EP-02	Metadata	Metadata Schema
EP-03	Job	Create Job
EP-03	Job	Job Status
EP-03	Job	Job History
EP-04	Queue	Queue Job
EP-04	Queue	Process Job
EP-04	Queue	Failed Job
EP-05	Result	Store JSON Result
EP-05	Result	Result Detail
EP-06	Prompt	CRUD Prompt
EP-06	Prompt	Prompt Version
EP-06	Prompt	Prompt/Action Mapping
EP-07	Subtitle	Audio → Subtitle
EP-07	Subtitle	Subtitle Correction
EP-07	Subtitle	Subtitle Versions
EP-08	Text	Subtitle → Full Text
EP-08	Text	Multi Model Processing
EP-09	Summary	Full Text → Summary
EP-09	Summary	Multi Model Summary
EP-10	Dataset	Create Dataset
EP-10	Dataset	Add Dataset Item
EP-10	Dataset	Generate Dataset
EP-11	Fine-tuning	Create Training Job
EP-11	Fine-tuning	Training Status
EP-11	Fine-tuning	Register Model
EP-12	Benchmark	Create Benchmark
EP-12	Benchmark	Execute Benchmark
EP-12	Benchmark	Store Metrics
EP-13	Lineage	Input → Result Trace
EP-13	Lineage	Result → Source Trace
EP-14	E2E	Full Pipeline



---

4. Definition of Done مخصوص هر User Story

برای اینکه پیمانکار نتواند بگوید «API را زدم، پس کار تمام است»، برای هر Story این DoD را قرار بدهید:

[ ] API implemented
[ ] API documented
[ ] Authentication checked
[ ] Authorization checked
[ ] Request validation implemented
[ ] Success response verified
[ ] Error response verified
[ ] Persistence verified
[ ] Entity ID returned
[ ] Created/Updated timestamp verified
[ ] Relationship with parent entity verified
[ ] Status lifecycle verified
[ ] Positive test passed
[ ] Negative test passed
[ ] Boundary test passed
[ ] Duplicate request tested
[ ] API response documented
[ ] Automated test exists
[ ] Feature/E2E test exists
[ ] No critical defect remains


---

5. یک نکته مهم درباره Unit Test

برای تحویل گرفتن Web Service نباید Unit Test را معیار اصلی قرار بدهیم.

سه سطح داشته باشید:

┌────────────────────┐
                    │   E2E / API Test   │
                    │  قرارداد تحویل     │
                    └─────────▲──────────┘
                              │
                    ┌─────────┴──────────┐
                    │   Feature Test     │
                    │   Workflow         │
                    └─────────▲──────────┘
                              │
                    ┌─────────┴──────────┐
                    │     Unit Test      │
                    │ Internal Components│
                    └────────────────────┘

Unit Test می‌گوید اجزای داخلی درست کار می‌کنند.

Feature Test می‌گوید یک قابلیت کامل درست کار می‌کند.

API/E2E Test می‌گوید:

> «اگر من مشتری این Web Service باشم، سیستم واقعاً همان چیزی را که قرارداد پروژه گفته تحویل می‌دهد یا نه؟»



برای تحویل پروژه، سطح سوم بسیار تعیین‌کننده است.


---

6. سناریوهای منفی که حتماً باید به Backlog اضافه شوند

فقط Happy Path کافی نیست.

مثلاً:

فایل

فایل بدون محتوا
فایل بسیار بزرگ
فرمت ناشناخته
MIME اشتباه
فایل خراب
نام فایل عجیب
آپلود تکراری

Metadata

Schema ناقص
Type اشتباه
Required Field حذف شده
Key تکراری
JSON نامعتبر

Job

content_id نامعتبر
action_type نامعتبر
model نامعتبر
prompt نامعتبر
ایجاد Job روی Content حذف‌شده

Processing

Model unavailable
Prompt unavailable
Worker failure
Timeout
Retry
Duplicate Job

Dataset

Dataset بدون Item
Item نامعتبر
Source حذف‌شده
Duplicate Item

Fine-tuning

Dataset خالی
Model نامعتبر
Training failure
Timeout

Benchmark

Model وجود ندارد
Dataset وجود ندارد
Dataset incompatible
Benchmark failure


---

نتیجه

اگر بخواهم این را برای تحویل واقعی پروژه تبدیل به یک سند رسمی کنم، پیشنهاد می‌کنم سند نهایی دقیقاً چهار لایه داشته باشد:

PRODUCT BACKLOG
       │
       ├── EPIC
       │     └── USER STORY
       │             ├── Acceptance Criteria
       │             ├── API Contract

صابر طباطبایی یزدی, [9/19/2026 12:33 PM]
│             ├── Positive Tests
       │             ├── Negative Tests
       │             └── Definition of Done
       │
       └── E2E Acceptance Scenarios
               │
               └── File
                   ↓
                 Job
                   ↓
                 Queue
                   ↓
                 Result
                   ↓
                Subtitle
                   ↓
             Corrected Subtitle
                   ↓
               Full Text
                   ↓
                Summary
                   ↓
                Dataset
                   ↓
              Fine-Tuning
                   ↓
              Final Model
                   ↓
               Benchmark

نکته کلیدی: در نسخه نهایی، برای هر Story بهتر است علاوه بر Acceptance Criteria، یک Test Case با Request، Response، Expected Status Code و Expected DB-visible state تعریف کنیم. این کار سند را از یک لیست نیازمندی به یک چک‌لیست رسمی تحویل و پذیرش Web Service تبدیل می‌کند.