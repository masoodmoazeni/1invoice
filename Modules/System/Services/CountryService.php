<?php

namespace Modules\System\Services;


use App\Services\BaseService;
use Modules\System\Repositories\CountryRepository;

class CountryService extends BaseService
{
    public function __construct(CountryRepository $country)
    {
        parent::__construct($country);
        $this->country = $country;
    }
}
