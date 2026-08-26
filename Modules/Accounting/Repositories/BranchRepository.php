<?php

namespace Modules\Accounting\Repositories;

use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Model;
use Modules\Accounting\Entities\Branch;

class BranchRepository extends BaseRepository
{
    protected Model $model;

    public function __construct(Branch $model)
    {
        $this->model = $model;
    }
}
