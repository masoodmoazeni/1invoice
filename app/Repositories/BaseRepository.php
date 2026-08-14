<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

abstract class BaseRepository
{
    protected Model $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * get all records
     */
    public function all(array $filters = [], array $columns = ['*'], array $relations = [], ?array $orderBy = null, ?int $limit = null): Collection
    {
        $query = $this->model->with($relations);
    
        $this->applyFilters($query, $filters);

        if (!empty($orderBy)) {
            foreach ($orderBy as $field => $direction) {
                $query->orderBy($field, $direction);
            }
        } else {
            $query->orderBy('id', 'asc');
        }

        if ($limit !== null && $limit > 0) {
            $query->limit($limit);
        }
        
        return $query->get($columns);
    }

    /**
     * get all records with paginate
     */
    public function paginate(int $perPage = 15, array $filters = [], array $orderBy = [], array $columns = ['*'], array $relations = []): LengthAwarePaginator
    {
        $query = $this->model->with($relations);
    
        $this->applyFilters($query, $filters);
        $this->applyOrderBy($query, $orderBy);
        
        return $query->paginate($perPage, $columns);
    }

    private function applyFilters($query, array $filters): void
    {
        foreach ($filters as $field => $value) {
            if ($value === null || $value === '') {
                continue;
            }

            if (!is_array($value)) {
                $query->where($field, $value);
                continue;
            }

            if (array_is_list($value)) {
                if (!empty($value)) {
                    $query->whereIn($field, $value);
                }
                continue;
            }

            foreach ($value as $operator => $operatorValue) {
                switch ($operator) {
                    case 'like':
                        if ($operatorValue !== null && $operatorValue !== '') {
                            $query->where($field, 'like', $operatorValue);
                        }
                        break;

                    case 'between':
                        if (is_array($operatorValue) && count($operatorValue) === 2) {
                            $query->whereBetween($field, $operatorValue);
                        }
                        break;
                        
                    case 'contain':
                        if ($operatorValue !== null && $operatorValue !== '') {
                            $query->where($field, 'like', '%' . $operatorValue . '%');
                        }
                        break;

                    case '>':
                    case '>=':
                    case '<':
                    case '<=':
                    case '!=':
                        $query->where($field, $operator, $operatorValue);
                        break;

                    case 'null':
                        if ($operatorValue === true) {
                            $query->whereNull($field);
                        }
                        break;

                    case 'not_null':
                        if ($operatorValue === true) {
                            $query->whereNotNull($field);
                        }
                        break;
                }
            }
        }
    }

    /**
     * apply order by conditions to query
     */
    protected function applyOrderBy($query, array $orderBy = []): void
    {
        if (!empty($orderBy) && isset($orderBy['status_custom'])) {
            $statusOrder = $orderBy['status_custom'];
            $statusOrderString = implode(',', $statusOrder);
            $query->orderByRaw("FIELD(status, {$statusOrderString})");
            
            if (isset($orderBy['id'])) {
                $query->orderBy('id', $orderBy['id']);
            }
            
            return;
        }

        if (!empty($orderBy) && isset($orderBy['id'])) {
            $query->orderBy('id', $orderBy['id']);
            return;
        }

        $query->orderBy('id', 'asc');
    }


    /**
     * get record with id
     */
    public function find(int $id, array $relations = []): ?Model
    {
        $query = $this->model->query();
    
        if (!empty($relations)) {
            $query->with($relations);
        }
        
        return $query->find($id);
    }

    /**
     * get record with condition
     */
    public function findBy(array $criteria, array $relations = []): ?Model
    {
        $query = $this->model->with($relations);
        
        foreach ($criteria as $key => $value) {
            $query->where($key, $value);
        }
        
        return $query->first();
    }

    /**
     * get record with condition
     */
    public function findByMultiple(array $criteria, array $relations = []): Collection
    {
        $query = $this->model->with($relations);
        
        foreach ($criteria as $key => $value) {
            $query->whereIn($key, $value);
        }
        
        return $query->get();
    }

    /**
     * get record with where
     */
    public function getWhere(array $criteria, array $relations = []): Collection
    {
        $query = $this->model->with($relations);
        
        foreach ($criteria as $key => $value) {
            $query->where($key, $value);
        }
        
        return $query->get();
    }

    /**
     * vreate new record
     */
    public function create(array $data): Model
    {
        return $this->model->create($data);
    }

    /**
     * create or update record
     */
    public function updateOrCreate(array $conditions, array $data): Model
    {
        return $this->model->updateOrCreate($conditions, $data);
    }

    public function findOrFail(int|string $id, array $relations = []): Model
    {
        $query = $this->model->newQuery();

        if (!empty($relations)) {
            $query->with($relations);
        }

        $record = $query->find($id);

        if (!$record) {
            throw new NotFoundHttpException(
                sprintf('%s with ID %s not found.', class_basename($this->model), $id)
            );
        }

        return $record;
    }

    public function lastOrFail(array $relations = [], string $column = 'id', array $columns = ['*']): ?Model
    {
        $query = $this->model->newQuery();

        if (!empty($relations)) {
            $query->with($relations);
        }

        return $query->orderBy($column, 'desc')->first($columns);
    }

    /**
     * update record
     */
    public function update(int $id, array $data): Model
    {
        $record = $this->find($id);
        $record->update($data);

        return $record->fresh();
    }

    /**
     * update bulk record
     */
    public function bulkUpdate(array $criteria, array $data): int
    {
        $query = $this->model->newQuery();
        
        foreach ($criteria as $key => $value) {
            $query->where($key, $value);
        }
        
        return $query->update($data);
    }

    /**
     * remove record
     */
    public function delete(int $id): bool
    {
        return (bool) $this->find($id)->delete();
    }

    /**
     * remove all record
     */
    public function deleteByCondition(array $criteria): int
    {
        $query = $this->model->newQuery();
        
        foreach ($criteria as $key => $value) {
            $query->where($key, $value);
        }
        
        return $query->delete();
    }

    /**
     * count record
     */
    public function count(array $criteria = []): int
    {
        $query = $this->model->newQuery();
        
        foreach ($criteria as $key => $value) {
            $query->where($key, $value);
        }
        
        return $query->count();
    }

    
    public function exists(array $criteria): bool
    {
        $query = $this->model->newQuery();
        
        foreach ($criteria as $key => $value) {
            $query->where($key, $value);
        }
        
        return $query->exists();
    }

    
    public function beginTransaction()
    {
        \DB::beginTransaction();
    }

   
    public function commit()
    {
        \DB::commit();
    }

    /**
     * run rollback
     */
    public function rollback()
    {
        \DB::rollBack();
    }

    /**
     * run transaction
     */
    public function transaction(\Closure $callback, int $attempts = 1)
    {
        return \DB::transaction($callback, $attempts);
    }
}