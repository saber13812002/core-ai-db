<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreSourceFileRequest;
use App\Http\Requests\Api\V1\UpdateSourceFileRequest;
use App\Http\Resources\Api\V1\SourceFileResource;
use App\Models\SourceFile;
use App\Services\SourceFileService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SourceFileController extends Controller
{
    public function __construct(protected SourceFileService $service) {}

    /**
     * @return array<int, string>
     */
    private function eagerLoad(): array
    {
        return ['supersededBy'];
    }

    public function index(Request $request): Response
    {
        $paginator = $this->service->list($request->integer('per_page', 15));
        $paginator->getCollection()->load($this->eagerLoad());

        return SourceFileResource::collection($paginator)->response();
    }

    public function store(StoreSourceFileRequest $request): Response
    {
        $model = $this->service->create($request->validated());
        $model->load($this->eagerLoad());

        return (new SourceFileResource($model))->response()->setStatusCode(201);
    }

    public function show(SourceFile $file): Response
    {
        $model = $this->service->findOrThrow($file->getKey());
        $model->load($this->eagerLoad());

        return (new SourceFileResource($model))->response();
    }

    public function update(UpdateSourceFileRequest $request, SourceFile $file): Response
    {
        $model = $this->service->update($file->getKey(), $request->validated());
        $model->load($this->eagerLoad());

        return (new SourceFileResource($model))->response();
    }

    public function destroy(SourceFile $file): Response
    {
        $this->service->delete($file->getKey());

        return response(null, 204);
    }
}
