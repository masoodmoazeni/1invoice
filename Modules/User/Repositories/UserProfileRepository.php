<?php

namespace Modules\User\Repositories;

use Illuminate\Support\Facades\DB;
use Modules\User\Entities\UserProfile;

class UserProfileRepository
{
    protected $model;

    public function __construct(UserProfile $model)
    {
        $this->model = $model;
    }

    public function saveProfile($userId, array $data)
    {
        $existing = $this->model->where('user_id', $userId)->first();
        if (!$existing) {
            if (!array_key_exists('sort_order', $data)) {
                $data['sort_order'] = (int) ($this->model->max('sort_order') ?? -1) + 1;
            }
            if (!array_key_exists('is_active', $data)) {
                $data['is_active'] = true;
            }
        }

        return $this->model->updateOrCreate(
            ['user_id' => $userId],
            $data
        );
    }

    // update profile
    public function updateProfile($memberId, array $data)
    {
        $profile = $this->model->find($memberId);
        if (!$profile) {
            return null;
        }

        $profile->update($data);

        return $profile->fresh(['user']);
    }

    public function getProfile($userId)
    {
        return $this->model
            ->with('user')
            ->where('user_id', $userId)
            ->first();
    }

    public function getProfileSlug($slug)
    {
        return $this->model
            ->with('user')
            ->where('slug', $slug)
            ->where('is_active', true)
            ->first();
    }

    /** HTML bytes to load for team list (full biography on show only). */
    private const LIST_BIOGRAPHY_PREVIEW_LENGTH = 800;

    /**
     * @return list<array{id: int, slug: string, created_at: ?string, updated_at: ?string}>
     */
    public function pluckSlugs(bool $onlyActive = true): array
    {
        $query = $this->model->newQuery()
            ->whereNotNull('slug')
            ->where('slug', '!=', '');

        if ($onlyActive) {
            $query->where('is_active', true);
        }

        return $query
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get(['id', 'slug', 'created_at', 'updated_at'])
            ->map(fn (UserProfile $profile) => [
                'id' => $profile->id,
                'slug' => $profile->slug,
                'created_at' => $profile->created_at?->toIso8601String(),
                'updated_at' => $profile->updated_at?->toIso8601String(),
            ])
            ->values()
            ->all();
    }

    public function getTeamMembers(int $perPage = 15, bool $onlyActive = false, bool $forList = false)
    {
        $query = $this->model->with('user')
            ->orderBy('sort_order')
            ->orderBy('id');

        if ($onlyActive) {
            $query->where('is_active', true);
        }

        if ($forList) {
            $table = $this->model->getTable();
            $query->select([
                "{$table}.id",
                "{$table}.user_id",
                "{$table}.slug",
                "{$table}.meta_title",
                "{$table}.meta_description",
                "{$table}.headline",
                "{$table}.summary",
                "{$table}.image",
                "{$table}.sort_order",
                "{$table}.is_active",
                "{$table}.created_at",
                "{$table}.updated_at",
            ])->addSelect(DB::raw(
                "SUBSTRING({$table}.biography, 1, " . self::LIST_BIOGRAPHY_PREVIEW_LENGTH . ') as biography_preview'
            ));
        }

        return $query->paginate($perPage);
    }

    public function updateTeamMemberSortOrder(int $memberId, int $sortOrder): void
    {
        $this->model->where('id', $memberId)->update(['sort_order' => $sortOrder]);
    }

    public function setMemberActive(int $memberId, ?bool $isActive = null): ?UserProfile
    {
        $profile = $this->model->find($memberId);
        if (!$profile) {
            return null;
        }

        $profile->update([
            'is_active' => $isActive !== null ? $isActive : !$profile->is_active,
        ]);

        return $profile->fresh(['user']);
    }

    public function findByUserId($user_id)
    {
        return $this->model->where('user_id', $user_id)->first();
    }

    /**
     * True when the user is an active SSC team broker (listed on /users/team).
     */
    public function isActiveTeamBroker(int $userId): bool
    {
        return $this->model
            ->where('user_id', $userId)
            ->where('is_active', true)
            ->whereHas('user', function ($query) {
                $query->whereHas('roles', function ($roleQuery) {
                    $roleQuery->where('name', 'broker');
                });
            })
            ->exists();
    }
}
