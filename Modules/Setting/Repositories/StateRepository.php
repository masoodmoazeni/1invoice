<?php

namespace Modules\Setting\Repositories;

use Modules\Setting\Entities\State;

class StateRepository
{
    protected $model;

    public function __construct(State $model)
    {
        $this->model = $model;
    }

    public function all()
    {
        return $this->model->all();
    }

    public function find($id)
    {
        return $this->model->find($id);
    }

    public function findByName($name)
    {
        $query = $this->model->newQuery();

        $query->whereRaw('LOWER(name) = ?', strtolower($name));

        return $query->first();
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update($id, array $data)
    {
        $record = $this->model->find($id);
        if (!$record) {
            return false;
        }

        $record->country_id    = $data['country_id'] ?? $record->country_id;
        $record->state_code    = $data['state_code'] ?? $record->state_code;
        $record->name          = $data['name'] ?? $record->name;
        $record->description   = $data['description'] ?? $record->description;
        $record->disclaimer    = $data['disclaimer'] ?? $record->disclaimer;
        $record->status        = $data['status'] ?? $record->status;

        $record->save($data);

        return $record;
    }

    public function delete($id)
    {
        $record = $this->model->find($id);
        if (!$record) {
            return false;
        }

        return $record->delete();
    }

    public function paginateWithSearch($perPage = 15, $search = null, $country_id=null)
    {
        $query = $this->model->with(['country']);

        if (!empty($search)) {
            $query->where('name', 'LIKE', "%{$search}%");
        }

        if (!empty($country_id)) {
            $query->where('country_id', $country_id);
        }
        return $query->paginate($perPage);
    }
}
