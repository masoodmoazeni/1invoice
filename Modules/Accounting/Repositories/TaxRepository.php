<?php

namespace Modules\Accounting\Repositories;

use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Model;
use Modules\Accounting\Entities\Tax;

class TaxRepository extends BaseRepository
{
    protected Model $model;

    public function __construct(Tax $model)
    {
        $this->model = $model;
    }
}
