<?php

namespace Modules\Company\Repositories;

use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Model;
use Modules\Company\Entities\Company;

class CompanyRepository extends BaseRepository
{
    protected Model $model;

    public function __construct(Company $model)
    {
        $this->model = $model;
    }
}
