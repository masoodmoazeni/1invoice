<?php

namespace Modules\Setting\Services;

use Modules\Setting\Repositories\StateRepository;

class StateService
{
    protected $states;

    public function __construct(StateRepository $states)
    {
        $this->states = $states;
    }

    public function create(array $data)
    {
        return $this->states->create($data);
    }

    public function update(int $id, array $data)
    {
        return $this->states->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->states->delete($id);
    }

    public function find(int $id)
    {
        return $this->states->find($id);
    }

    public function findByName(string $name)
    {
        return $this->states->findByName($name);
    }

    public function all()
    {
        return $this->states->all();
    }

    public function getPaginatedCountries($perPage = 15, $search = null, $country_id = null)
    {
        return $this->states->paginateWithSearch($perPage, $search, $country_id);
    }
}
