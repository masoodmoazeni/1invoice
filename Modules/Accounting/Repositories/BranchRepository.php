<?php

namespace Modules\Accounting\Repositories;

use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Model;
use Modules\Accounting\Entities\Branches;

class BranchRepository extends BaseRepository
{
    protected Model $model;

    public function __construct(Branches $model)
    {
        $this->model = $model;
    }
}