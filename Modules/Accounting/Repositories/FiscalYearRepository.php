<?php

namespace Modules\Accounting\Repositories;

use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Model;
use Modules\Accounting\Entities\FiscalYear;

class FiscalYearRepository extends BaseRepository
{
    protected Model $model;

    public function __construct(FiscalYear $model)
    {
        $this->model = $model;
    }
}
