<?php

namespace Modules\Accounting\Services;


use App\Services\BaseService;
use Modules\Accounting\Repositories\CurrencyRepository;

class CurrencyService extends BaseService
{
    public function __construct(CurrencyRepository $currency)
    {
        parent::__construct($currency);
        $this->currency = $currency;
    }
}
