<?php

namespace Modules\Accounting\Repositories;

use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Model;
use Modules\Accounting\Entities\TaxGroup;

class TaxGroupRepository extends BaseRepository
{
    protected Model $model;

    public function __construct(TaxGroup $model)
    {
        $this->model = $model;
    }
}
