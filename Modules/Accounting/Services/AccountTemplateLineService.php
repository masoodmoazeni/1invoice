<?php

namespace Modules\Accounting\Services;


use App\Services\BaseService;
use Modules\Accounting\Repositories\AccountTemplateLineRepository;

class AccountTemplateLineService extends BaseService
{
    public function __construct(AccountTemplateLineRepository $accountTemplateLine)
    {
        parent::__construct($accountTemplateLine);
        $this->accountTemplateLine = $accountTemplateLine;
    }
}
