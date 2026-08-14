<?php

namespace Modules\Accounting\Services;


use App\Services\BaseService;
use Modules\Accounting\Repositories\CompanyRepository;

class CompanyService extends BaseService
{
    protected $company;

    public function __construct(CompanyRepository $company)
    {
        $this->company = $company;
    }
}
