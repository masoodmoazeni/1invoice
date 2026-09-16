<?php

namespace Modules\Company\Services;


use App\Services\BaseService;
use Modules\Company\Repositories\FiscalYearRepository;

class FiscalYearService extends BaseService
{
    public function __construct(FiscalYearRepository $fiscalYear)
    {
        parent::__construct($fiscalYear);
        $this->fiscalYear = $fiscalYear;
    }
}
