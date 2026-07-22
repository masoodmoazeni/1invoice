<?php

namespace Modules\Setting\Services;


use Modules\Setting\Repositories\PackageRepository;

class PackageService
{
    protected $packages;

    public function __construct(PackageRepository $packages)
    {
        $this->packages = $packages;
    }

    public function create(array $data)
    {
        return $this->packages->create($data);
    }

    public function update(int $id, array $data)
    {
        return $this->packages->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->packages->delete($id);
    }

    public function find(int $id)
    {
        return $this->packages->find($id);
    }

    public function findByName(string $name)
    {
        return $this->packages->findByName($name);
    }

    public function all(array $filters = [])
    {
        return $this->packages->all($filters);
    }
}
