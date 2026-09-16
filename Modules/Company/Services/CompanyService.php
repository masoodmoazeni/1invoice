<?php

namespace Modules\Company\Services;


use App\Services\BaseService;
use Modules\Company\Repositories\CompanyRepository;

class CompanyService extends BaseService
{
    public function __construct(CompanyRepository $company)
    {
        parent::__construct($company);
        $this->company = $company;
    }
}
