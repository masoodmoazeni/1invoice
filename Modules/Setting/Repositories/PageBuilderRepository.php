<?php

namespace Modules\Setting\Repositories;

use Modules\Setting\Entities\Page;

class PageBuilderRepository
{
    protected $model;

    public function __construct(Page $model)
    {
        $this->model = $model;
    }

    /**
     * @return list<array{id: int, slug: string, created_at: ?string, updated_at: ?string}>
     */
    public function pluckSlugs(array $filters = []): array
    {
        $query = $this->model->newQuery()
            ->whereNotNull('published_slug')
            ->where('published_slug', '!=', '');

        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query
            ->orderByDesc('created_at')
            ->get(['id', 'published_slug', 'created_at', 'updated_at'])
            ->map(fn (Page $page) => [
                'id' => $page->id,
                'slug' => $page->published_slug,
                'created_at' => $page->created_at?->toIso8601String(),
                'updated_at' => $page->updated_at?->toIso8601String(),
            ])
            ->values()
            ->all();
    }

    public function all(int $perPage = 15, array $filters = [])
    {
        $query = $this->model->newQuery();

        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function find($id)
    {
        return $this->model->findOrFail($id);
    }

    public function findBySlug($slug)
    {
        return $this->model
            ->where('slug', $slug)
            ->first();
    }

    public function findPublishedBySlug($slug)
    {
        return $this->model
            ->published()
            ->where(function ($query) use ($slug) {
                $query->where('published_slug', $slug)
                    ->orWhere(function ($fallback) use ($slug) {
                        $fallback
                            ->whereNull('published_slug')
                            ->where('slug', $slug);
                    });
            })
            ->first();
    }

    public function create(array $data)
    {
        $data['status'] = Page::STATUS_DRAFT;

        if (!empty($data['is_published'])) {
            $this->applyPublishedSnapshot($data);
        }

        return $this->model->create($data);
    }

    public function update(int $id, array $data)
    {
        $record = $this->model->find($id);
        if (!$record) {
            return false;
        }

        $record->fill([
            'title'   => $data['title']   ?? $record->title,
            'slug'    => $data['slug']    ?? $record->slug,
            'type'    => $data['type']    ?? $record->type,
            'image'   => $data['image']   ?? $record->image,
            'content' => $data['content'] ?? $record->content,
            'root'    => $data['root']    ?? $record->root,
        ]);

        if (!empty($data['is_published'])) {
            $record->status = Page::STATUS_PUBLISHED;
            $record->published_at = $data['published_at'] ?? now();
            $record->published_title = $record->title;
            $record->published_slug = $record->slug;
            $record->published_content = $record->content;
            $record->published_root = $record->root;
        }

        $record->save();

        return $record;
    }

    public function delete(int $id)
    {
        $record = $this->model->find($id);
        if (!$record) {
            return false;
        }

        return $record->delete();
    }

    private function applyPublishedSnapshot(array &$data): void
    {
        $data['status'] = Page::STATUS_PUBLISHED;
        $data['published_at'] = $data['published_at'] ?? now();
        $data['published_title'] = $data['title'] ?? '';
        $data['published_slug'] = $data['slug'] ?? '';
        $data['published_content'] = $data['content'] ?? [];
        $data['published_root'] = $data['root'] ?? [];
    }
}
