<?php

namespace Modules\Accounting\Repositories;

use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Model;
use Modules\Accounting\Entities\Currency;

class CurrencyRepository extends BaseRepository
{
    protected Model $model;

    public function __construct(Currency $model)
    {
        $this->model = $model;
    }
}
