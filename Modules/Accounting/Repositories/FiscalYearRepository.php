<?php

namespace Modules\Accounting\Repositories;

use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Model;
use Modules\Accounting\Entities\FacialYears;

class FacialYearRepository extends BaseRepository
{
    protected Model $model;

    public function __construct(FacialYears $model)
    {
        $this->model = $model;
    }
}