<?php

namespace Modules\Setting\Repositories;

use Modules\Setting\Entities\Package;

class PackageRepository
{
    protected $model;

    public function __construct(Package $model)
    {
        $this->model = $model;
    }

    public function all(array $filters = [])
    {
        $query = $this->model->newQuery();

        if (!empty($filters['type_package'])) {
            $query->where('type_package', $filters['type_package'])
                ->where('type_stripe', 'one-time');
        }

        return $query->get();
    }

    public function find($id)
    {
        return $this->model->find($id);
    }

    public function findByName($name)
    {
        $query = $this->model->newQuery();

        $query->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($name) . '%']);

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

        // no update price - monthly price
        // beacuse no update in stripe server
        $record->title           = $data['title'] ?? $record->title;
        $record->description     = $data['description'] ?? $record->description;
        $record->status          = $data['status'] ?? $record->status;

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

    public function paginateWithSearch($search = null, $country_id = null, $state_id = null)
    {
        $query = $this->model->query();

        if (!empty($search)) {
            $query->where('name', 'LIKE', "%{$search}%");
        }

        if (!empty($country_id)) {
            $query->where('country_id', $country_id);
        }

        if (!empty($state_id)) {
            $query->where('state_id', $state_id);
        }

        return $query->get();
    }
}
