<?php

namespace Modules\Accounting\Services;


use App\Services\BaseService;
use Modules\Accounting\Repositories\TaxGroupRepository;

class TaxGroupService extends BaseService
{
    public function __construct(TaxGroupRepository $taxGroup)
    {
        parent::__construct($taxGroup);
        $this->taxGroup = $taxGroup;
    }
}
