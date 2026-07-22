<?php

namespace Modules\Setting\Services;


use Modules\Setting\Repositories\CityRepository;

class CityService
{
    protected $cities;

    public function __construct(CityRepository $cities)
    {
        $this->cities = $cities;
    }

    public function create(array $data)
    {
        return $this->cities->create($data);
    }

    public function update(int $id, array $data)
    {
        return $this->cities->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->cities->delete($id);
    }

    public function find(int $id)
    {
        return $this->cities->find($id);
    }

    public function findByName(string $name)
    {
        return $this->cities->findByName($name);
    }

    public function all()
    {
        return $this->cities->all();
    }

    public function getPaginatedCountries($perPage = 15, $search = null, $country_id = null, $state_id = null)
    {
        return $this->cities->paginateWithSearch($perPage, $search, $country_id, $state_id);
    }
}
