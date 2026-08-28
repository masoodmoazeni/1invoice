<?php

namespace Modules\Accounting\Repositories;

use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Model;
use Modules\Accounting\Entities\NumberSequence;

class NumberSequenceRepository extends BaseRepository
{
    protected Model $model;

    public function __construct(NumberSequence $model)
    {
        $this->model = $model;
    }
}
