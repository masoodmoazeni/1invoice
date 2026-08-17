<?php

namespace Modules\Accounting\Services;


use App\Services\BaseService;
use Modules\Accounting\Repositories\AccountRepository;

class AccountService extends BaseService
{
    public function __construct(AccountRepository $account)
    {
        parent::__construct($account);
        $this->account = $account;
    }
}
