<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\FileDownloadException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreSourceFileRequest;
use App\Http\Requests\Api\V1\UpdateSourceFileRequest;
use App\Http\Requests\Api\V1\UploadSourceFileRequest;
use App\Http\Resources\Api\V1\SourceFileResource;
use App\Models\SourceFile;
use App\Services\FileUploadService;
use App\Services\SourceFileService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class SourceFileController extends Controller
{
    public function __construct(
        protected SourceFileService $service,
        protected FileUploadService $uploadService,
    ) {}

    /**
     * @return array<int, string>
     */
    private function eagerLoad(): array
    {
        return ['supersededBy', 'metadataSchema'];
    }

    public function index(Request $request): Response
    {
        $metadataFilters = $request->input('metadata');
        $filters = is_array($metadataFilters) ? $metadataFilters : null;

        $paginator = $this->service->listWithMetadataFilters($filters, $request->integer('per_page', 15));
        $paginator->getCollection()->load($this->eagerLoad());

        return SourceFileResource::collection($paginator)->response();
    }

    public function store(StoreSourceFileRequest $request): Response
    {
        $model = $this->service->create($this->withCreator($request, $request->validated()));
        $model->load($this->eagerLoad());

        return (new SourceFileResource($model))->response()->setStatusCode(201);
    }

    /**
     * Multipart upload: stores the binary on the configured disk and
     * registers a SourceFile row (processing_status = registered).
     * Identical content (same sha256) returns the existing row with 200
     * and "duplicate": true instead of creating a second row.
     */
    public function upload(UploadSourceFileRequest $request): JsonResponse
    {
        $result = $this->uploadService->upload(
            $request->file('file'),
            $request->validated(),
            $request->attributes->get('api_key')?->id,
        );
        $result->file->load($this->eagerLoad());

        return (new SourceFileResource($result->file))
            ->additional(['duplicate' => $result->duplicate])
            ->response()
            ->setStatusCode($result->duplicate ? 200 : 201);
    }

    /**
     * Streams the stored binary with its original filename and mime type.
     * 410 when the row exists but the binary is gone from disk.
     */
    public function download(SourceFile $file): Response
    {
        $disk = Storage::disk((string) config('ai-factory.upload.disk'));
        $path = $file->storage_path;

        if (! $disk->exists($path)) {
            throw FileDownloadException::notFound();
        }

        return $disk->response($path, $file->original_filename ?: (string) $file->id, [], 'attachment');
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
