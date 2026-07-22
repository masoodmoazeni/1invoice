<?php

// namespace Modules\Core\Services;
namespace Modules\User\Services;

use Exception;
use Illuminate\Support\Facades\Log;

abstract class BaseService
{
    protected $repository;

    public function __construct($repository)
    {
        $this->repository = $repository;
    }

    public function getAll()
    {
        try {
            return $this->repository->all();
        } catch (Exception $ex) {
            Log::error($ex->getMessage());
            throw new Exception('Failed to fetch data');
        }
    }

    public function getPagination($perPage = 15)
    {
        try {
            return $this->repository->paginate($perPage);
        } catch (Exception $ex) {
            Log::error($ex->getMessage());
            throw new Exception('Failed to fetch paginated data');
        }
    }

    public function find($id)
    {
        try {
            return $this->repository->find($id);
        } catch (Exception $ex) {
            Log::error($ex->getMessage());
            throw new Exception('Failed to find record');
        }
    }

    public function create(array $data)
    {
        try {
            return $this->repository->create($data);
        } catch (Exception $ex) {
            Log::error($ex->getMessage());
            throw new Exception('Failed to create record');
        }
    }

    public function update($id, array $data)
    {
        try {
            return $this->repository->update($id, $data);
        } catch (Exception $ex) {
            Log::error($ex->getMessage());
            throw new Exception('Failed to update record');
        }
    }

    public function delete($id)
    {
        try {
            return $this->repository->delete($id);
        } catch (Exception $ex) {
            Log::error($ex->getMessage());
            throw new Exception('Failed to delete record');
        }
    }
}
