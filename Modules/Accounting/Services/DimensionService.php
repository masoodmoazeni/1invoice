<?php

namespace Modules\Accounting\Services;


use App\Services\BaseService;
use Modules\Accounting\Repositories\DimensionRepository;

class DimensionService extends BaseService
{
    public function __construct(DimensionRepository $dimension)
    {
        parent::__construct($dimension);
        $this->dimension = $dimension;
    }
}
