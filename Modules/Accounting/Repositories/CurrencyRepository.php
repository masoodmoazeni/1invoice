<?php

namespace Modules\Accounting\Repositories;

use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Model;
use Modules\Accounting\Entities\Currencies;

class CurrencyRepository extends BaseRepository
{
    protected Model $model;

    public function __construct(Currencies $model)
    {
        $this->model = $model;
    }
}