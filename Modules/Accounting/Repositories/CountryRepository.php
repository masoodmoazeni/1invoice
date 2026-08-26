<?php

namespace Modules\Accounting\Repositories;

use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Model;
use Modules\Accounting\Entities\Country;

class CountryRepository extends BaseRepository
{
    protected Model $model;

    public function __construct(Country $model)
    {
        $this->model = $model;
    }
}
