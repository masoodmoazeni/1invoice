<?php

namespace Modules\Accounting\Repositories;

use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Model;
use Modules\Accounting\Entities\Countries;

class CountryRepository extends BaseRepository
{
    protected Model $model;

    public function __construct(Countries $model)
    {
        $this->model = $model;
    }
}