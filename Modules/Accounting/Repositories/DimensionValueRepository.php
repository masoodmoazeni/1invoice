<?php

namespace Modules\Accounting\Repositories;

use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Model;
use Modules\Accounting\Entities\DimensionValue;

class DimensionValueRepository extends BaseRepository
{
    protected Model $model;

    public function __construct(DimensionValue $model)
    {
        $this->model = $model;
    }
}
