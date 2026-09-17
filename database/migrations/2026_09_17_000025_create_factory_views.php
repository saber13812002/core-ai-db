<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::connection()->getDriverName() !== 'pgsql') {
            return;
        }

        DB::statement(<<<'SQL'
            CREATE OR REPLACE VIEW v_latest_outputs AS
            SELECT *
            FROM processed_outputs
            WHERE is_latest = TRUE AND superseded_by_id IS NULL AND deleted_at IS NULL
        SQL);

        DB::statement(<<<'SQL'
            CREATE OR REPLACE VIEW v_execution_matrix AS
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
                WHERE aa2.is_active = TRUE
                  AND EXISTS (
                      SELECT 1
                      FROM jsonb_array_elements_text(aa2.input_file_types) AS value
                      WHERE value = sf.file_type
                  )
            ) aa
            LEFT JOIN processed_outputs po
                ON po.source_file_id = sf.id AND po.action_id = aa.id AND po.deleted_at IS NULL
            LEFT JOIN automation_jobs aj ON aj.id = po.job_id
            LEFT JOIN models m ON m.id = po.model_id
            LEFT JOIN prompts p ON p.id = po.prompt_id
            WHERE sf.deleted_at IS NULL
        SQL);

        DB::statement(<<<'SQL'
            CREATE OR REPLACE VIEW v_benchmark_comparison AS
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
            LEFT JOIN models bm ON bm.id = bpo.model_id
        SQL);
    }

    public function down(): void
    {
        if (DB::connection()->getDriverName() !== 'pgsql') {
            return;
        }

        foreach (['v_benchmark_comparison', 'v_execution_matrix', 'v_latest_outputs'] as $view) {
            if (Schema::hasView($view)) {
                DB::statement("DROP VIEW {$view}");
            }
        }
    }
};
