<?php

namespace Modules\User\Services;

use App\Services\FileUploadService;
use Modules\User\Repositories\UserProfileRepository;

class UserProfileService
{
    protected $repo;

    public function __construct(
        UserProfileRepository $repo,
        protected FileUploadService $fileUploadService
    ) {
        $this->repo = $repo;
    }

    public function saveProfile($userId, array $data)
    {
        return $this->repo->saveProfile($userId, $data);
    }

    public function updateProfile($memberId, array $data)
    {
        return $this->repo->updateProfile($memberId, $data);
    }

    public function getProfile($userId)
    {
        return $this->repo->getProfile($userId);
    }

    public function getProfileSlug($slug)
    {
        return $this->repo->getProfileSlug($slug);
    }

    public function getTeamMembers(int $perPage = 15, bool $onlyActive = false, bool $forList = false)
    {
        return $this->repo->getTeamMembers($perPage, $onlyActive, $forList);
    }

    /**
     * @return list<array{id: int, slug: string, created_at: ?string, updated_at: ?string}>
     */
    public function pluckSlugs(bool $onlyActive = true): array
    {
        return $this->repo->pluckSlugs($onlyActive);
    }

    public function updateTeamMemberSortOrder(int $memberId, int $sortOrder): void
    {
        $this->repo->updateTeamMemberSortOrder($memberId, $sortOrder);
    }

    public function setMemberActive(int $memberId, ?bool $isActive = null)
    {
        return $this->repo->setMemberActive($memberId, $isActive);
    }

    public function findByUserId(int $user_id)
    {
        return $this->repo->findByUserId($user_id);
    }

    public function uploadImage($image)
    {
        if (!$image) {
            throw new \Exception('Invalid uploaded file');
        }

        $result = $this->fileUploadService->uploadImageAsWebp(
            $image,
            'images/teams',
            ['filename_style' => 'timestamp']
        );

        return [
            'filename' => $result['filename'],
            'file_url' => $result['url'],
        ];
    }
}
