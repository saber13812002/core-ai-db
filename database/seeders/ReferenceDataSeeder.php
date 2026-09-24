<?php

namespace Database\Seeders;

use App\Models\AiModel;
use App\Models\AutomationAction;
use App\Models\AutomationFlow;
use App\Models\MasterPrompt;
use App\Models\OutputType;
use App\Models\Project;
use App\Models\ServiceRegistry;
use App\Models\SourceType;
use Illuminate\Database\Seeder;
use Ramsey\Uuid\Uuid;

class ReferenceDataSeeder extends Seeder
{
    /**
     * Seed stable reference data (idempotent by natural keys).
     */
    public function run(): void
    {
        $this->seedOutputTypes();
        $this->seedSourceTypes();
        $this->seedProjects();
        $this->seedActions();
        $this->seedModels();
        $this->seedServices();
        $this->seedFlows();
        $this->seedPrompts();
    }

    protected function seedSourceTypes(): void
    {
        $types = [
            ['code' => 'audio', 'label_fa' => 'صوت'],
            ['code' => 'video', 'label_fa' => 'ویدیو'],
            ['code' => 'pdf', 'label_fa' => 'PDF'],
            ['code' => 'docx', 'label_fa' => 'Word'],
            ['code' => 'xlsx', 'label_fa' => 'Excel'],
            ['code' => 'pptx', 'label_fa' => 'PowerPoint'],
            ['code' => 'image', 'label_fa' => 'تصویر'],
            ['code' => 'text', 'label_fa' => 'متنی'],
        ];

        foreach ($types as $type) {
            SourceType::updateOrCreate(['code' => $type['code']], $type);
        }
    }

    protected function seedProjects(): void
    {
        Project::updateOrCreate(
            ['name' => 'مجمع حکمت خراسانی'],
            [
                'description' => 'پروژه‌ی محتوای هوشمند مجمع حکمت خراسانی',
                'is_active' => true,
            ],
        );

        Project::updateOrCreate(
            ['name' => 'سمائه'],
            [
                'description' => 'جلسه‌های صوتی سمائه (درمانی/روان‌شناسی)',
                'is_active' => true,
            ],
        );
    }

    protected function seedOutputTypes(): void
    {
        $types = [
            ['code' => 'lecture-transcript', 'name_fa' => 'متن سخنرانی', 'output_format' => 'text', 'supports_chunk' => true],
            ['code' => 'slides-extracted', 'name_fa' => 'اسلایدهای استخراج‌شده', 'output_format' => 'json'],
            ['code' => 'quiz-questions', 'name_fa' => 'سؤالات آزمون', 'output_format' => 'json'],
            ['code' => 'summary', 'name_fa' => 'خلاصه', 'output_format' => 'text'],
            ['code' => 'cleaned-text', 'name_fa' => 'متن پاک‌سازی‌شده', 'output_format' => 'text', 'supports_chunk' => true],
        ];

        foreach ($types as $type) {
            OutputType::updateOrCreate(['code' => $type['code']], $type);
        }
    }

    protected function seedActions(): void
    {
        $actions = [
            [
                'code' => 'extract-transcript',
                'name_fa' => 'استخراج متن سخنرانی',
                'action_category' => 'extraction',
                'input_file_types' => ['pdf', 'mp3', 'mp4'],
                'output_type_id' => OutputType::where('code', 'lecture-transcript')->value('id'),
                'requires_prompt' => true,
            ],
            [
                'code' => 'extract-slides',
                'name_fa' => 'استخراج اسلایدها',
                'action_category' => 'extraction',
                'input_file_types' => ['pdf'],
                'output_type_id' => OutputType::where('code', 'slides-extracted')->value('id'),
                'requires_prompt' => true,
            ],
            [
                'code' => 'generate-quiz',
                'name_fa' => 'ساخت سؤالات آزمون',
                'action_category' => 'generation',
                'input_file_types' => ['pdf', 'docx'],
                'output_type_id' => OutputType::where('code', 'quiz-questions')->value('id'),
                'requires_prompt' => true,
                'is_batchable' => false,
            ],
            [
                'code' => 'summarize',
                'name_fa' => 'خلاصه‌سازی',
                'action_category' => 'summarization',
                'input_file_types' => ['pdf', 'docx', 'mp3'],
                'output_type_id' => OutputType::where('code', 'summary')->value('id'),
                'requires_prompt' => true,
            ],
            [
                'code' => 'refine-text',
                'name_fa' => 'اصلاح و تنظیم متن',
                'action_category' => 'cleaning',
                'input_file_types' => ['pdf', 'docx', 'mp3', 'mp4'],
                'output_type_id' => OutputType::where('code', 'cleaned-text')->value('id'),
                'requires_prompt' => true,
            ],
            [
                'code' => 'clean-text',
                'name_fa' => 'پاک‌سازی متن',
                'action_category' => 'cleaning',
                'input_file_types' => ['pdf', 'docx'],
                'output_type_id' => OutputType::where('code', 'cleaned-text')->value('id'),
                'requires_prompt' => true,
            ],
        ];

        foreach ($actions as $action) {
            AutomationAction::updateOrCreate(['code' => $action['code']], $action);
        }
    }

    protected function seedModels(): void
    {
        $models = [
            [
                'code' => 'deepseek-chat-v3',
                'name_fa' => 'دیپ‌سیک چت V3',
                'provider' => 'deepseek',
                'model_type' => 'llm',
                'version' => 'v3',
                'context_window' => 65536,
            ],
            [
                'code' => 'gpt-4o',
                'name_fa' => 'GPT-4o',
                'provider' => 'openai',
                'model_type' => 'llm',
                'version' => '4o',
                'context_window' => 128000,
            ],
            [
                'code' => 'gpt-4o-mini',
                'name_fa' => 'GPT-4o Mini',
                'provider' => 'openai',
                'model_type' => 'llm',
                'version' => '4o-mini',
                'context_window' => 128000,
            ],
            [
                'code' => 'bge-m3',
                'name_fa' => 'BGE-M3',
                'provider' => 'bge',
                'model_type' => 'embedder',
                'version' => 'm3',
                'context_window' => 8192,
            ],
        ];

        foreach ($models as $model) {
            AiModel::updateOrCreate(['code' => $model['code']], $model);
        }
    }

    protected function seedServices(): void
    {
        $services = [
            [
                'name' => 'LLM Gateway',
                'service_type' => 'llm',
                'base_url' => 'http://llm-gateway:8000',
                'endpoints' => ['chat' => '/v1/chat/completions', 'embeddings' => '/v1/embeddings'],
                'health_status' => 'healthy',
                'last_health_check' => now(),
            ],
            [
                'name' => 'Vector Store',
                'service_type' => 'vector-db',
                'base_url' => 'http://chromadb:8001',
                'endpoints' => ['collections' => '/api/v1/collections', 'query' => '/api/v1/collections/{id}/query'],
                'health_status' => 'healthy',
                'last_health_check' => now(),
            ],
            [
                'name' => 'Training Service',
                'service_type' => 'training',
                'base_url' => 'http://training:8002',
                'endpoints' => ['jobs' => '/v1/jobs'],
                'health_status' => 'healthy',
                'last_health_check' => now(),
            ],
        ];

        foreach ($services as $service) {
            ServiceRegistry::updateOrCreate(['name' => $service['name']], $service);
        }
    }

    protected function seedFlows(): void
    {
        $flows = [
            [
                'name' => 'PDF به آزمون',
                'platform' => 'n8n',
                'platform_flow_id' => 'flow-quiz-from-pdf',
                'description' => 'دریافت PDF و ساخت سؤالات آزمون',
                'input_file_types' => ['pdf'],
                'output_type_id' => OutputType::where('code', 'quiz-questions')->value('id'),
                'action_id' => AutomationAction::where('code', 'generate-quiz')->value('id'),
            ],
            [
                'name' => 'صدای سخنرانی به متن',
                'platform' => 'n8n',
                'platform_flow_id' => 'flow-audio-transcript',
                'description' => 'دریافت فایل صوتی و استخراج متن',
                'input_file_types' => ['mp3', 'mp4'],
                'output_type_id' => OutputType::where('code', 'lecture-transcript')->value('id'),
                'action_id' => AutomationAction::where('code', 'extract-transcript')->value('id'),
            ],
        ];

        foreach ($flows as $flow) {
            AutomationFlow::updateOrCreate(
                ['platform' => $flow['platform'], 'platform_flow_id' => $flow['platform_flow_id']],
                $flow,
            );
        }
    }

    protected function seedPrompts(): void
    {
        $prompts = [
            [
                'family_code' => 'transcript-extractor',
                'name' => 'استخراج متن سخنرانی',
                'prompt_type' => 'extraction',
                'target_output_type_code' => 'lecture-transcript',
                'target_action_code' => 'extract-transcript',
                'content' => 'You are a transcription assistant. Transcribe the provided lecture content into clean Persian text. Preserve technical terms as-is.',
            ],
            [
                'family_code' => 'quiz-generator',
                'name' => 'ساخت سؤالات آزمون',
                'prompt_type' => 'extraction',
                'target_output_type_code' => 'quiz-questions',
                'target_action_code' => 'generate-quiz',
                'content' => 'You are an exam designer. Generate multiple-choice questions from the provided course material, covering key concepts.',
            ],
            [
                'family_code' => 'cleaning',
                'name' => 'پاک‌سازی متن',
                'prompt_type' => 'cleaning',
                'target_output_type_code' => 'cleaned-text',
                'target_action_code' => 'clean-text',
                'content' => 'Clean the provided text: fix spacing, normalize punctuation, remove duplicates, and keep the original meaning.',
            ],
            [
                'family_code' => 'refiner',
                'name' => 'اصلاح و تنظیم متن',
                'prompt_type' => 'cleaning',
                'target_output_type_code' => 'cleaned-text',
                'target_action_code' => 'refine-text',
                'content' => 'Refine the raw transcript into flowing, correct Persian: fix ASR errors, restore sentence boundaries, and keep all names and technical terms intact.',
            ],
            [
                'family_code' => 'summarizer',
                'name' => 'خلاصه‌سازی متن',
                'prompt_type' => 'summarization',
                'target_output_type_code' => 'summary',
                'target_action_code' => 'summarize',
                'content' => 'Summarize the provided text in Persian: keep the key points, preserve the order of topics, and stay within three paragraphs.',
            ],
            [
                'family_code' => 'judge',
                'name' => 'داوری کیفیت خروجی',
                'prompt_type' => 'judging',
                'target_output_type_code' => null,
                'target_action_code' => null,
                'content' => 'Compare the candidate output with the ground truth paragraph by paragraph and score quality from 0 to 100.',
            ],
        ];

        foreach ($prompts as $prompt) {
            $familyId = Uuid::uuid5(Uuid::NAMESPACE_DNS, $prompt['family_code'])->toString();
            $content = $prompt['content'];

            MasterPrompt::updateOrCreate(
                ['family_id' => $familyId, 'version' => '1.0'],
                [
                    'name' => $prompt['name'],
                    'content' => $content,
                    'content_hash' => hash('sha256', $content),
                    'prompt_type' => $prompt['prompt_type'],
                    'purpose' => 'Seed prompt v1.0',
                    'target_output_type_id' => $prompt['target_output_type_code']
                        ? OutputType::where('code', $prompt['target_output_type_code'])->value('id')
                        : null,
                    'target_action_id' => $prompt['target_action_code']
                        ? AutomationAction::where('code', $prompt['target_action_code'])->value('id')
                        : null,
                    'is_active' => true,
                    'tags' => [$prompt['prompt_type']],
                ],
            );
        }
    }
}
