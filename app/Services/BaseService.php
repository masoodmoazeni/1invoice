<?php

namespace App\Services;

use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

abstract class BaseService
{
    protected BaseRepository $repository;
    
    /**
     * Default error messages
     */
    protected function getErrorMessages(): array
    {
        return [
            'create' => __('messages.global.base.error.create'),
            'update' => __('messages.global.base.error.update'),
            'delete' => __('messages.global.base.error.delete'),
            'not_found' => __('messages.global.base.error.not_found'),
        ];
    }

    /**
     * Get success messages
     */
    protected function getSuccessMessages(): array
    {
        return [
            'create' => __('messages.global.base.success.create'),
            'update' => __('messages.global.base.success.update'),
            'delete' => __('messages.global.base.success.delete'),
        ];
    }
    
    public function __construct(BaseRepository $repository)
    {
        $this->repository = $repository;
    }
    
    /**
     * get all records
     */
    public function getAll(array $filter = [], array $columns = ['*'], array $relations = [], ?array $orderBy = null, ?int $limit = null): Collection
    {
        return $this->repository->all($filter, $columns, $relations ,$orderBy, $limit);
    }
    
    /**
     * get all record with paginate
     */
    public function getPaginate(int $perPage = 15, array $filter = [], array $orderBy = [], array $columns = ['*'], array $relations = []): LengthAwarePaginator
    {
        return $this->repository->paginate($perPage, $filter, $orderBy, $columns, $relations);
    }
    
    /**
     * get record with id
     */
    public function find(int $id, array $relations = []): ?Model
    {
        return $this->repository->find($id, $relations);
    }

    /**
     * get record with id
     */
    public function findBy(array $criteria, array $relations = []): ?Model
    {
        return $this->repository->findBy($criteria, $relations);
    }

    /**
     * get record with id
     */
    public function findByMultiple(array $criteria, array $relations = []): Collection
    {
        return $this->repository->findByMultiple($criteria, $relations);
    }
    
    /**
     * Creating a new record with validation and events
     */
    public function create(array $data): array
    {
        try {
            $record = $this->repository->create($data);
            
            return $this->successResponse($record, $this->getSuccessMessages()['create']);
            
        } catch (\Exception $e) {
            return $this->errorResponse($e, __('messages.global.base.error.try_catch'));
        }
    }
    
    /**
     * Updating records with validation and events
     */
    public function update(int $id, array $data): array
    {
        try {
            $existingRecord = $this->repository->find($id);
            
            if (!$existingRecord) {
                return $this->errorResponse(null, $this->getErrorMessages()['not_found'], 404);
            }
            
            $record = $this->repository->update($id, $data);
            
            return $this->successResponse($record, $this->getSuccessMessages()['update']);
            
        } catch (\Exception $e) {
            return $this->errorResponse($e, __('messages.global.base.error.try_catch'));
        }
    }
    
    /**
     * Deleting records with events
     */
    public function delete(int $id): array
    {
        try {
            $record = $this->repository->find($id);
            
            if (!$record) {
                return $this->errorResponse(null, $this->getErrorMessages()['not_found'], 404);
            }
            
            $result = $this->repository->delete($id);
            
            if ($result) {
                return $this->successResponse($record, $this->getSuccessMessages()['delete']);
            }
            
            return $this->errorResponse(null, $this->getErrorMessages()['delete']);
            
        } catch (\Exception $e) {
            return $this->errorResponse($e, __('messages.global.base.error.try_catch'));
        }
    }

    /**
     * Deleting records with events
     */
    public function deleteByCondition(array $data): array
    {
        try {
            $record = $this->repository->findby($data);
            
            if (!$record) {
                return $this->errorResponse(null, $this->getErrorMessages()['not_found'], 404);
            }
            
            $result = $this->repository->deleteByCondition($data);
            
            if ($result) {
                return $this->successResponse($record, $this->getSuccessMessages()['delete']);
            }
            
            return $this->errorResponse(null, $this->getErrorMessages()['delete']);
            
        } catch (\Exception $e) {
            return $this->errorResponse($e, __('messages.global.base.error.try_catch'));
        }
    }
    
    /**
     * Creating or updating a record
     */
    public function updateOrCreate(array $conditions, array $data): array
    {
        try {
            $record = $this->repository->updateOrCreate($conditions, $data);
            $message = $record->wasRecentlyCreated ? $this->getSuccessMessages()['create'] : $this->getSuccessMessages()['update'];
            
            return $this->successResponse($record, $message);
            
        } catch (\Exception $e) {
            return $this->errorResponse($e, __('messages.global.base.error.try_catch'));
        }
    }

    public function findOrFail(int|string $id, array $relations = []): Model
    {
        return $this->repository->findOrFail($id, $relations);
    }

    public function lastOrFail(array $relations = [], string $column = 'id', array $columns = ['*']): ?Model
    {
        return $this->repository->lastOrFail($relations, $column, $columns);
    }
    
    /**
     * In-transaction operation execution
     */
    public function transactional(\Closure $callback, int $attempts = 1)
    {
        return DB::transaction($callback, $attempts);
    }
    
    /**
     * successresponse
     */
    protected function successResponse($data, string $message, int $statusCode = 200): array
    {
        return [
            'success' => true,
            'message' => $message,
            'data' => $data,
            'status_code' => $statusCode,
        ];
    }
    
    /**
     * error response
     */
    protected function errorResponse(\Exception|string|null $error, string $message, int $statusCode = 500): array
    {
        $errorDetails = null;
        
        if ($error instanceof \Exception) {
            $errorDetails = $error->getMessage();
            Log::error($message . ': ' . $errorDetails, [
                'trace' => $error->getTraceAsString(),
                'file' => $error->getFile(),
                'line' => $error->getLine(),
            ]);
        } elseif (is_string($error)) {
            $errorDetails = $error;
            Log::error($message . ': ' . $errorDetails);
        }
        
        return [
            'success' => false,
            'message' => $message,
            'error' => $errorDetails,
            'status_code' => $statusCode,
        ];
    }
}