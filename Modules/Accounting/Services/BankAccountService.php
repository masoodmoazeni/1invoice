<?php

namespace Modules\Accounting\Services;


use App\Services\BaseService;
use Modules\Accounting\Repositories\BankAccountRepository;

class BankAccountService extends BaseService
{
    public function __construct(BankAccountRepository $bankaccount)
    {
        parent::__construct($bankaccount);
        $this->bankaccount = $bankaccount;
    }
}
