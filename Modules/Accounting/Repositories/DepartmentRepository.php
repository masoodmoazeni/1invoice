<?php

namespace Modules\Accounting\Repositories;

use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Model;
use Modules\Accounting\Entities\Departments;

class DepartmentRepository extends BaseRepository
{
    protected Model $model;

    public function __construct(Departments $model)
    {
        $this->model = $model;
    }
}