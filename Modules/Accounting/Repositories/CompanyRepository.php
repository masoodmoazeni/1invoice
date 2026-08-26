<?php

namespace Modules\Accounting\Repositories;

use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Model;
use Modules\Accounting\Entities\Company;

class CompanyRepository extends BaseRepository
{
    protected Model $model;

    public function __construct(Company $model)
    {
        $this->model = $model;
    }
}
