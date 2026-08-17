<?php

namespace Modules\Accounting\Services;


use App\Services\BaseService;
use Modules\Accounting\Repositories\ExchangeRateRepository;

class ExchangeRateService extends BaseService
{
    public function __construct(ExchangeRateRepository $exchangerate)
    {
        parent::__construct($exchangerate);
        $this->exchangerate = $exchangerate;
    }
}
