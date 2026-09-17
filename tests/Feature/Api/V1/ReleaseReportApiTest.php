<?php

namespace Tests\Feature\Api\V1;

use App\Models\ReleaseReport;
use Illuminate\Database\Eloquent\Model;

class ReleaseReportApiTest extends CrudApiTestCase
{
    protected function modelClass(): string
    {
        return ReleaseReport::class;
    }

    protected function tableName(): string
    {
        return 'release_reports';
    }

    protected function collectionUrl(Model $record): string
    {
        return 'api/v1/release-reports';
    }

    protected function updateFieldsFor(Model $record): array
    {
        return ['storage_path' => 'reports/updated-report'];
    }
}
