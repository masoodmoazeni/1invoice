<?php

namespace Modules\System\Services;


use App\Services\BaseService;
use Modules\System\Repositories\CurrencyRepository;

class CurrencyService extends BaseService
{
    public function __construct(CurrencyRepository $currency)
    {
        parent::__construct($currency);
        $this->currency = $currency;
    }
}
