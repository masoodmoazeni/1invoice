<?php

namespace Modules\Accounting\Services;


use App\Services\BaseService;
use Modules\Accounting\Repositories\TaxRepository;

class TaxService extends BaseService
{
    public function __construct(TaxRepository $tax)
    {
        parent::__construct($tax);
        $this->tax = $tax;
    }
}
