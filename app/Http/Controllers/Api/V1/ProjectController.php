<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreProjectRequest;
use App\Http\Requests\Api\V1\UpdateProjectRequest;
use App\Http\Resources\Api\V1\ProjectResource;
use App\Models\Project;
use App\Services\ProjectService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ProjectController extends Controller
{
    public function __construct(protected ProjectService $service) {}

    public function index(Request $request): Response
    {
        $paginator = $this->service->list($request->integer('per_page', 15));

        return ProjectResource::collection($paginator)->response();
    }

    public function store(StoreProjectRequest $request): Response
    {
        $model = $this->service->create($request->validated());

        return (new ProjectResource($model))->response()->setStatusCode(201);
    }

    public function show(Project $project): Response
    {
        $model = $this->service->findOrThrow($project->getKey());

        return (new ProjectResource($model))->response();
    }

    public function update(UpdateProjectRequest $request, Project $project): Response
    {
        $model = $this->service->update($project->getKey(), $request->validated());

        return (new ProjectResource($model))->response();
    }

    public function destroy(Project $project): Response
    {
        $this->service->delete($project->getKey());

        return response(null, 204);
    }
}
