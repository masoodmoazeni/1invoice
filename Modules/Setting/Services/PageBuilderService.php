<?php

namespace Modules\Setting\Services;

use App\Services\FileUploadService;
use Modules\Setting\Repositories\PageBuilderRepository;

class PageBuilderService
{
    protected $repository;

    public function __construct(
        PageBuilderRepository $repository,
        protected FileUploadService $fileUploadService
    ) {
        $this->repository = $repository;
    }

    public function paginate(int $perPage = 15, array $filters = [])
    {
        return $this->repository->all($perPage, $filters);
    }

    /**
     * @return list<array{id: int, slug: string, created_at: ?string, updated_at: ?string}>
     */
    public function pluckSlugs(array $filters = []): array
    {
        return $this->repository->pluckSlugs($filters);
    }

    public function find($id)
    {
        return $this->repository->find($id);
    }

    public function findBySlug($slug)
    {
        return $this->repository->findBySlug($slug);
    }

    public function findPublishedBySlug($slug)
    {
        return $this->repository->findPublishedBySlug($slug);
    }

    public function create(array $data)
    {
        return $this->repository->create($data);
    }

    public function update($id, array $data)
    {
        return $this->repository->update($id, $data);
    }

    public function delete($id)
    {
        return $this->repository->delete($id);
    }

    public function uploadImage($image)
    {
        if (!$image) {
            throw new \Exception('Invalid uploaded file');
        }

        $result = $this->fileUploadService->uploadImageAsWebp(
            $image,
            'images/pagebuilder',
            ['filename_style' => 'timestamp']
        );

        return ['filename' => $result['filename'], 'file_url' => $result['url']];
    }
}
