<?php

namespace Modules\Setting\Repositories;

use Illuminate\Support\Facades\DB;
use Modules\Setting\Entities\BlogPost;

class BlogPostRepository
{
    /** Bytes of HTML to read from DB for list views (full body on show only). */
    private const LIST_CONTENT_PREVIEW_LENGTH = 1200;

    /**
     * @return list<array{id: int, slug: string, created_at: ?string, updated_at: ?string}>
     */
    public function pluckSlugs(array $filters = []): array
    {
        $query = BlogPost::query()->whereNotNull('slug')->where('slug', '!=', '');
        $this->applyListFilters($query, $filters);

        return $query
            ->orderByDesc('id')
            ->get(['id', 'slug', 'created_at', 'updated_at'])
            ->map(fn (BlogPost $post) => [
                'id' => $post->id,
                'slug' => $post->slug,
                'created_at' => $post->created_at?->toIso8601String(),
                'updated_at' => $post->updated_at?->toIso8601String(),
            ])
            ->values()
            ->all();
    }

    public function paginate(int $perPage = 15, array $filters = [])
    {
        $query = BlogPost::query();
        $this->applyListFilters($query, $filters);

        $table = (new BlogPost())->getTable();

        $query->select([
            "{$table}.id",
            "{$table}.title",
            "{$table}.slug",
            "{$table}.featured_image",
            "{$table}.status",
            "{$table}.published_at",
            "{$table}.meta_title",
            "{$table}.meta_description",
            "{$table}.og_image",
            "{$table}.created_at",
            "{$table}.updated_at",
            "{$table}.deleted_at",
        ])->addSelect(DB::raw(
            "SUBSTRING({$table}.content, 1, " . self::LIST_CONTENT_PREVIEW_LENGTH . ') as content_preview'
        ));

        return $query
            ->latest("{$table}.id")
            ->paginate($perPage);
    }

    private function applyListFilters($query, array $filters): void
    {
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%");
            });
        }
    }

    public function create(array $data): BlogPost
    {
        return BlogPost::create($data);
    }

    public function update(BlogPost $post, array $data): BlogPost
    {
        $post->update($data);
        return $post->fresh();
    }

    public function findById(int $id): ?BlogPost
    {
        return BlogPost::find($id);
    }

    public function findBySlug(string $slug): ?BlogPost
    {
        return BlogPost::where('slug', $slug)->first();
    }

    public function delete(BlogPost $post): void
    {
        $post->delete();
    }
}
