<?php

namespace Modules\Company\Repositories;

use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Model;
use Modules\Company\Entities\Department;

class DepartmentRepository extends BaseRepository
{
    protected Model $model;

    public function __construct(Department $model)
    {
        $this->model = $model;
    }
}
