<?php

namespace Modules\Accounting\Repositories;

use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Model;
use Modules\Accounting\Entities\Dimensions;

class DimensionRepository extends BaseRepository
{
    protected Model $model;

    public function __construct(Dimensions $model)
    {
        $this->model = $model;
    }
}