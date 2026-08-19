<?php

namespace Modules\Accounting\Repositories;

use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Model;
use Modules\Accounting\Entities\Taxes;

class TaxRepository extends BaseRepository
{
    protected Model $model;

    public function __construct(Taxes $model)
    {
        $this->model = $model;
    }
}