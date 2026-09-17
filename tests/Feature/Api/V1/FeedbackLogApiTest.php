<?php

namespace Tests\Feature\Api\V1;

use App\Models\FeedbackLog;
use Illuminate\Database\Eloquent\Model;

class FeedbackLogApiTest extends CrudApiTestCase
{
    protected function modelClass(): string
    {
        return FeedbackLog::class;
    }

    protected function tableName(): string
    {
        return 'feedback_logs';
    }

    protected function collectionUrl(Model $record): string
    {
        return 'api/v1/feedbacks';
    }

    protected function storeOverrides(Model $record): array
    {
        return [
            'cleaned_output_id' => null,
            'trained_model_id' => null,
            'user_id' => null,
            'session_id' => null,
            'comment' => null,
        ];
    }

    protected function updateFieldsFor(Model $record): array
    {
        return ['comment' => 'Updated feedback comment'];
    }
}
