<?php

namespace Modules\Accounting\Repositories;

use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Model;
use Modules\Accounting\Entities\Department;

class DepartmentRepository extends BaseRepository
{
    protected Model $model;

    public function __construct(Department $model)
    {
        $this->model = $model;
    }
}
