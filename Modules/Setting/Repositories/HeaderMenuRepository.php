<?php

namespace Modules\Setting\Repositories;

use Illuminate\Support\Facades\DB;
use Modules\Setting\Entities\HeaderMenuItem;

class HeaderMenuRepository
{
    public function getPublishedTree(): array
    {
        return HeaderMenuItem::query()
            ->whereNull('parent_id')
            ->where('status', HeaderMenuItem::STATUS_PUBLISHED)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->with([
                'page',
                'publishedChildren' => fn ($query) => $query->with('page'),
            ])
            ->get()
            ->all();
    }

    public function getAdminTree(): array
    {
        return HeaderMenuItem::query()
            ->whereNull('parent_id')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->with([
                'page',
                'children' => fn ($query) => $query->with('page'),
            ])
            ->get()
            ->all();
    }

    public function findById(int $id): ?HeaderMenuItem
    {
        return HeaderMenuItem::with(['page', 'children.page', 'parent'])->find($id);
    }

    public function create(array $data): HeaderMenuItem
    {
        if (!isset($data['sort_order'])) {
            $data['sort_order'] = $this->nextSortOrder($data['parent_id'] ?? null);
        }

        return HeaderMenuItem::create($data);
    }

    public function update(HeaderMenuItem $item, array $data): HeaderMenuItem
    {
        $item->update($data);

        return $item->fresh(['page', 'children.page', 'parent']);
    }

    public function delete(HeaderMenuItem $item): void
    {
        $item->children()->delete();
        $item->delete();
    }

    public function reorder(?int $parentId, array $orderedIds): void
    {
        DB::transaction(function () use ($parentId, $orderedIds) {
            foreach ($orderedIds as $index => $id) {
                $query = HeaderMenuItem::query()->where('id', $id);

                if ($parentId === null) {
                    $query->whereNull('parent_id');
                } else {
                    $query->where('parent_id', $parentId);
                }

                $query->update(['sort_order' => $index + 1]);
            }
        });
    }

    public function countTopLevel(): int
    {
        return HeaderMenuItem::query()->whereNull('parent_id')->count();
    }

    private function nextSortOrder(?int $parentId): int
    {
        $max = HeaderMenuItem::query()
            ->where('parent_id', $parentId)
            ->max('sort_order');

        return ((int) $max) + 1;
    }
}
