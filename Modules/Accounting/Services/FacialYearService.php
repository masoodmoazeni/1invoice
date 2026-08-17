<?php

namespace Modules\Accounting\Services;


use App\Services\BaseService;
use Modules\Accounting\Repositories\FacialYearRepository;

class FacialYearService extends BaseService
{
    public function __construct(FacialYearRepository $facialyear)
    {
        parent::__construct($facialyear);
        $this->facialyear = $facialyear;
    }
}
