<?php

namespace Modules\Accounting\Services;


use App\Services\BaseService;
use Modules\Accounting\Repositories\AccountTemplateRepository;

class AccountTemplateService extends BaseService
{
    public function __construct(AccountTemplateRepository $accounttemplate)
    {
        parent::__construct($accounttemplate);
        $this->accounttemplate = $accounttemplate;
    }
}
