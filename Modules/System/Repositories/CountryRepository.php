<?php

namespace Modules\System\Repositories;

use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Model;
use Modules\System\Entities\Country;

class CountryRepository extends BaseRepository
{
    protected Model $model;

    public function __construct(Country $model)
    {
        $this->model = $model;
    }
}
