<?php

namespace Modules\Accounting\Repositories;

use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Model;
use Modules\Accounting\Entities\Partner;

class PartnerRepository extends BaseRepository
{
    protected Model $model;

    public function __construct(Partner $model)
    {
        $this->model = $model;
    }
}
