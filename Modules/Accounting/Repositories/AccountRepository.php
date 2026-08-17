<?php

namespace Modules\Accounting\Repositories;

use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Model;
use Modules\Accounting\Entities\Accounts;

class AccountRepository extends BaseRepository
{
    protected Model $model;

    public function __construct(Accounts $model)
    {
        $this->model = $model;
    }
}