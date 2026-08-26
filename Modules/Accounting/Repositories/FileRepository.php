<?php

namespace Modules\Accounting\Repositories;

use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Model;
use Modules\Accounting\Entities\ExchangeRate;

class ExchangeRateRepository extends BaseRepository
{
    protected Model $model;

    public function __construct(ExchangeRate $model)
    {
        $this->model = $model;
    }
}
