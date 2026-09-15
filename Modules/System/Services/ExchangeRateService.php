<?php

namespace Modules\System\Services;


use App\Services\BaseService;
use Modules\System\Repositories\ExchangeRateRepository;

class ExchangeRateService extends BaseService
{
    public function __construct(ExchangeRateRepository $exchangerate)
    {
        parent::__construct($exchangerate);
        $this->exchangerate = $exchangerate;
    }
}
