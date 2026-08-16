<?php

namespace Modules\Accounting\Repositories;

use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Model;
use Modules\Accounting\Entities\TimeZones;

class TimeZoneRepository extends BaseRepository
{
    protected Model $model;

    public function __construct(TimeZones $model)
    {
        $this->model = $model;
    }
}