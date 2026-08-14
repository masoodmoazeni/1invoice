<?php

namespace Modules\Accounting\Repositories;

use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Model;
use Modules\Accounting\Entities\Companies;

class CompanyRepository extends BaseRepository
{
    protected Model $model;

    public function __construct(Companies $model)
    {
        $this->model = $model;
    }
}