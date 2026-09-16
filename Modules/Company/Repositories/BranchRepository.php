<?php

namespace Modules\Company\Repositories;

use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Model;
use Modules\Company\Entities\Branch;

class BranchRepository extends BaseRepository
{
    protected Model $model;

    public function __construct(Branch $model)
    {
        $this->model = $model;
    }
}
