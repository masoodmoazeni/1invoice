<?php

namespace Modules\Accounting\Repositories;

use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Model;
use Modules\Accounting\Entities\BankAccount;

class BankAccountRepository extends BaseRepository
{
    protected Model $model;

    public function __construct(BankAccount $model)
    {
        $this->model = $model;
    }
}
