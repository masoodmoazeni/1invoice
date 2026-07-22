<?php

namespace Modules\Setting\Services;

use Modules\Setting\Entities\HeaderMenuItem;
use Modules\Setting\Entities\Page;
use Modules\Setting\Repositories\HeaderMenuRepository;

class HeaderMenuService
{
    public function __construct(protected HeaderMenuRepository $repo)
    {
    }

    public function getPublishedMenu(): array
    {
        return $this->repo->getPublishedTree();
    }

    public function getAdminMenu(): array
    {
        return $this->repo->getAdminTree();
    }

    public function find(int $id): HeaderMenuItem
    {
        $item = $this->repo->findById($id);

        if (!$item) {
            throw new \Exception('Header menu item not found');
        }

        return $item;
    }

    public function create(array $data): HeaderMenuItem
    {
        $this->validateItemData($data);

        if (!empty($data['is_dropdown'])) {
            $data['href'] = null;
            $data['link_type'] = HeaderMenuItem::LINK_TYPE_CUSTOM;
            $data['page_id'] = null;
        }

        if (!empty($data['parent_id'])) {
            $parent = $this->find((int) $data['parent_id']);

            if (!$parent->is_dropdown || $parent->parent_id !== null) {
                throw new \Exception('Parent must be a top-level dropdown menu');
            }

            $data['is_dropdown'] = false;
            $data['cols'] = null;
        }

        return $this->repo->create($data);
    }

    public function update(int $id, array $data): HeaderMenuItem
    {
        $item = $this->find($id);
        $this->validateItemData($data, $item);

        if ($item->is_dropdown && array_key_exists('is_dropdown', $data) && !$data['is_dropdown']) {
            if ($item->children()->exists()) {
                throw new \Exception('Remove dropdown children before converting to a link');
            }
        }

        if (!empty($data['is_dropdown'])) {
            $data['href'] = null;
            $data['link_type'] = HeaderMenuItem::LINK_TYPE_CUSTOM;
            $data['page_id'] = null;
        }

        if ($item->parent_id !== null) {
            $data['is_dropdown'] = false;
            $data['cols'] = null;
        }

        return $this->repo->update($item, $data);
    }

    public function delete(int $id): void
    {
        $item = $this->find($id);
        $this->repo->delete($item);
    }

    public function reorder(?int $parentId, array $orderedIds): void
    {
        if ($parentId !== null) {
            $this->find($parentId);
        }

        $query = HeaderMenuItem::query()->whereIn('id', $orderedIds);

        if ($parentId === null) {
            $query->whereNull('parent_id');
        } else {
            $query->where('parent_id', $parentId);
        }

        if ($query->count() !== count($orderedIds)) {
            throw new \Exception('Invalid menu item order payload');
        }

        $this->repo->reorder($parentId, $orderedIds);
    }

    private function validateItemData(array $data, ?HeaderMenuItem $existing = null): void
    {
        $isDropdown = $data['is_dropdown'] ?? $existing?->is_dropdown ?? false;
        $parentId = $data['parent_id'] ?? $existing?->parent_id;
        $linkType = $data['link_type'] ?? $existing?->link_type ?? HeaderMenuItem::LINK_TYPE_CUSTOM;

        if ($isDropdown && $parentId) {
            throw new \Exception('Dropdown children cannot contain nested dropdowns');
        }

        if (!$isDropdown && !$parentId) {
            if ($linkType === HeaderMenuItem::LINK_TYPE_PAGE_BUILDER) {
                $pageId = $data['page_id'] ?? $existing?->page_id;

                if (!$pageId) {
                    throw new \Exception('Page builder link requires a page');
                }

                $page = Page::find($pageId);

                if (!$page) {
                    throw new \Exception('Selected page was not found');
                }
            } elseif (empty($data['href']) && !($existing?->href)) {
                throw new \Exception('Link menu item requires an href');
            }
        }
    }
}
