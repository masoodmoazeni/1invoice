<?php

namespace Modules\Accounting\Repositories;

use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Model;
use Modules\Accounting\Entities\Dimension;

class DimensionRepository extends BaseRepository
{
    protected Model $model;

    public function __construct(Dimension $model)
    {
        $this->model = $model;
    }
}
