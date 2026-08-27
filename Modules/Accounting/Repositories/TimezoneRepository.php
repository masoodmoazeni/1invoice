<?php

namespace Modules\Accounting\Repositories;

use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Model;
use Modules\Accounting\Entities\TimeZone;

class TimeZoneRepository extends BaseRepository
{
    protected Model $model;

    public function __construct(TimeZone $model)
    {
        $this->model = $model;
    }
}
