<?php

namespace Modules\Setting\Services;

use App\Services\FileUploadService;
use Modules\Setting\Entities\BlogPost;
use Modules\Setting\Repositories\BlogPostRepository;

class BlogPostService
{
    protected BlogPostRepository $repo;

    public function __construct(
        BlogPostRepository $repo,
        protected FileUploadService $fileUploadService
    ) {
        $this->repo = $repo;
    }

    public function paginate(int $perPage = 15, array $filters = [])
    {
        return $this->repo->paginate($perPage, $filters);
    }

    /**
     * @return list<array{id: int, slug: string, created_at: ?string, updated_at: ?string}>
     */
    public function pluckSlugs(array $filters = []): array
    {
        return $this->repo->pluckSlugs($filters);
    }

    public function find(int $id): BlogPost
    {
        $post = $this->repo->findById($id);

        if (!$post) {
            throw new \Exception('Blog post not found');
        }

        return $post;
    }

    public function findBySlug(string $slug): BlogPost
    {
        $post = $this->repo->findBySlug($slug);

        if (!$post) {
            throw new \Exception('Blog post not found');
        }

        return $post;
    }

    public function create(array $data): BlogPost
    {
        if (($data['status'] ?? null) === 'published') {
            $data['published_at'] = $data['published_at'] ?? now();
        }

        return $this->repo->create($data);
    }

    public function update(int $id, array $data): BlogPost
    {
        $post = $this->find($id);

        if (($data['status'] ?? null) === 'published' && !$post->published_at) {
            $data['published_at'] = now();
        }

        return $this->repo->update($post, $data);
    }

    public function delete(int $id): void
    {
        $post = $this->find($id);
        $this->repo->delete($post);
    }

    public function uploadImage($image)
    {
        if (!$image) {
            throw new \Exception('Invalid uploaded file');
        }

        $result = $this->fileUploadService->uploadImageAsWebp(
            $image,
            'images/blogs',
            ['filename_style' => 'timestamp']
        );

        return [
            'filename' => $result['filename'],
            'file_url' => $result['url'],
        ];
    }
}
