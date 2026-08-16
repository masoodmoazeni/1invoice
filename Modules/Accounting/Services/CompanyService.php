<?php

namespace Modules\Accounting\Services;


use App\Services\BaseService;
use Modules\Accounting\Repositories\CompanyRepository;

class CompanyService extends BaseService
{
    public function __construct(CompanyRepository $company)
    {
        parent::__construct($company);
        $this->company = $company;
    }
}
