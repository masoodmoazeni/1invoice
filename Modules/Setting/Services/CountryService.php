<?php

namespace Modules\Setting\Services;

use Modules\Setting\Repositories\CountryRepository;

class CountryService
{
    protected $countries;

    public function __construct(CountryRepository $countries)
    {
        $this->countries = $countries;
    }


    public function create(array $data)
    {
        return $this->countries->create($data);
    }

    public function update(int $id, array $data)
    {
        return $this->countries->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->countries->delete($id);
    }

    public function find(int $id)
    {
        return $this->countries->find($id);
    }

    public function findByName(string $name)
    {
        return $this->countries->findByName($name);
    }

    public function all()
    {
        return $this->countries->all();
    }
    
    public function getPaginatedCountries($perPage = 15, $search = null)
    {
        return $this->countries->paginateWithSearch($perPage, $search);
    }

    public function getAllHierarchy()
    {
        return $this->countries->allWithHierarchy();
    }

    public function getAllHierarchyWithCountList($id = [], $country_id = [], $state_id = [], $city_id = [])
    {
        return $this->countries->allWithHierarchyWithCountList($id, $country_id, $state_id, $city_id);
    }


}
