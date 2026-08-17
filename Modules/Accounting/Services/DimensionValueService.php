<?php

namespace Modules\Accounting\Services;


use App\Services\BaseService;
use Modules\Accounting\Repositories\DimensionValueRepository;

class DimensionValueService extends BaseService
{
    public function __construct(DimensionValueRepository $dimensionvalue)
    {
        parent::__construct($dimensionvalue);
        $this->dimensionvalue = $dimensionvalue;
    }
}
