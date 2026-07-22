<?php

namespace Modules\Setting\Services;

use Modules\Setting\Repositories\SaleAdvantageRepository;

class SaleAdvantageService
{
    protected $saleAdvantages;

    public function __construct(SaleAdvantageRepository $saleAdvantages)
    {
        $this->saleAdvantages = $saleAdvantages;
    }

    public function create(array $data)
    {
        return $this->saleAdvantages->create($data);
    }

    public function update(int $id, array $data)
    {
        return $this->saleAdvantages->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->saleAdvantages->delete($id);
    }

    public function find(int $id)
    {
        return $this->saleAdvantages->find($id);
    }

    public function all()
    {
        return $this->saleAdvantages->all();
    }

    public function getPagination($perPage = 15, $search = null, $type = null)
    {
        return $this->saleAdvantages->paginateWithSearch($perPage, $search, $type);
    }

    public function getAllTypesTree()
    {
        $types = ['business', 'sale', 'occupancy'];
        // $types = ['sale'];
        $result = [];

        foreach ($types as $type) {
            $result[$type] = $this->saleAdvantages->getByType($type);
        }

        return $result;
    }

    public function getAllTypesTreeWithCountList($ids = [])
    {
        $types = ['business', 'sale', 'occupancy'];

        $result = [];

        foreach ($types as $type) {
            $result[$type] = $this->saleAdvantages->getAllTypesTreeWithCountList($type, $ids);
        }

        return $result;
    }
}
