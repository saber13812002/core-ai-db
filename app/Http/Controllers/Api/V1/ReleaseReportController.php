<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreReleaseReportRequest;
use App\Http\Requests\Api\V1\UpdateReleaseReportRequest;
use App\Http\Resources\Api\V1\ReleaseReportResource;
use App\Models\ReleaseReport;
use App\Services\ReleaseReportService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ReleaseReportController extends Controller
{
    public function __construct(protected ReleaseReportService $service) {}

    /**
     * @return array<int, string>
     */
    private function eagerLoad(): array
    {
        return ['release'];
    }

    public function index(Request $request): Response
    {
        $paginator = $this->service->list($request->integer('per_page', 15));
        $paginator->getCollection()->load($this->eagerLoad());

        return ReleaseReportResource::collection($paginator)->response();
    }

    public function store(StoreReleaseReportRequest $request): Response
    {
        $model = $this->service->create($request->validated());
        $model->load($this->eagerLoad());

        return (new ReleaseReportResource($model))->response()->setStatusCode(201);
    }

    public function show(ReleaseReport $releaseReport): Response
    {
        $model = $this->service->findOrThrow($releaseReport->getKey());
        $model->load($this->eagerLoad());

        return (new ReleaseReportResource($model))->response();
    }

    public function update(UpdateReleaseReportRequest $request, ReleaseReport $releaseReport): Response
    {
        $model = $this->service->update($releaseReport->getKey(), $request->validated());
        $model->load($this->eagerLoad());

        return (new ReleaseReportResource($model))->response();
    }

    public function destroy(ReleaseReport $releaseReport): Response
    {
        $this->service->delete($releaseReport->getKey());

        return response(null, 204);
    }
}
