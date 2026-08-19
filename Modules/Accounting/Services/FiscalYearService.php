<?php

namespace Modules\Accounting\Services;


use App\Services\BaseService;
use Modules\Accounting\Repositories\FiscalYearRepository;

class FiscalYearService extends BaseService
{
    public function __construct(FiscalYearRepository $fiscalYear)
    {
        parent::__construct($fiscalYear);
        $this->fiscalYear = $fiscalYear;
    }
}
