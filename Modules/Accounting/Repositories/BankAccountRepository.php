<?php

namespace Modules\Accounting\Repositories;

use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Model;
use Modules\Accounting\Entities\BankAccounts;

class BankAccountRepository extends BaseRepository
{
    protected Model $model;

    public function __construct(BankAccounts $model)
    {
        $this->model = $model;
    }
}