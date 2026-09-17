<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreMasterPromptRequest;
use App\Http\Requests\Api\V1\UpdateMasterPromptRequest;
use App\Http\Resources\Api\V1\MasterPromptResource;
use App\Models\MasterPrompt;
use App\Services\MasterPromptService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MasterPromptController extends Controller
{
    public function __construct(protected MasterPromptService $service) {}

    /**
     * @return array<int, string>
     */
    private function eagerLoad(): array
    {
        return ['targetOutputType', 'targetAction', 'parentPrompt'];
    }

    public function index(Request $request): Response
    {
        $paginator = $this->service->list($request->integer('per_page', 15));
        $paginator->getCollection()->load($this->eagerLoad());

        return MasterPromptResource::collection($paginator)->response();
    }

    public function store(StoreMasterPromptRequest $request): Response
    {
        $model = $this->service->create($request->validated());
        $model->load($this->eagerLoad());

        return (new MasterPromptResource($model))->response()->setStatusCode(201);
    }

    public function show(MasterPrompt $prompt): Response
    {
        $model = $this->service->findOrThrow($prompt->getKey());
        $model->load($this->eagerLoad());

        return (new MasterPromptResource($model))->response();
    }

    public function update(UpdateMasterPromptRequest $request, MasterPrompt $prompt): Response
    {
        $model = $this->service->update($prompt->getKey(), $request->validated());
        $model->load($this->eagerLoad());

        return (new MasterPromptResource($model))->response();
    }

    public function destroy(MasterPrompt $prompt): Response
    {
        $this->service->delete($prompt->getKey());

        return response(null, 204);
    }
}
