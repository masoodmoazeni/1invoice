<?php

namespace Modules\Company\Repositories;

use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Model;
use Modules\Company\Entities\FiscalYear;

class FiscalYearRepository extends BaseRepository
{
    protected Model $model;

    public function __construct(FiscalYear $model)
    {
        $this->model = $model;
    }
}
