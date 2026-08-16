<?php

namespace Modules\Accounting\Services;


use App\Services\BaseService;
use Modules\Accounting\Repositories\CountryRepository;

class CountryService extends BaseService
{
    public function __construct(CountryRepository $country)
    {
        parent::__construct($country);
        $this->country = $country;
    }
}
