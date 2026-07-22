<?php

namespace Modules\Setting\Services;

use Modules\Setting\Repositories\CommentRepository;

class CommentService
{
    protected $repository;

    public function __construct(CommentRepository $repository)
    {
        $this->repository = $repository;
    }

    public function listAll()
    {
        return $this->repository->all();
    }

    public function findById($id)
    {
        return $this->repository->find($id);
    }

    public function createComment($userId, array $data)
    {
        $data['user_id'] = $userId;
        $data['status'] = 0; // پیش‌فرض: نیاز به تایید مدیر
        return $this->repository->create($data);
    }

    public function updateComment($id, array $data)
    {
        return $this->repository->update($id, $data);
    }

    public function deleteComment($id)
    {
        return $this->repository->delete($id);
    }

    public function listActive()
    {
        return $this->repository->getActive();
    }

    public function listByUser($userId)
    {
        return $this->repository->getByUser($userId);
    }

    public function getPaginatedComments($perPage = 15)
    {
        return $this->repository->paginate($perPage);
    }

}
