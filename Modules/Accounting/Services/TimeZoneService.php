<?php

namespace Modules\Accounting\Services;


use App\Services\BaseService;
use Modules\Accounting\Repositories\TimeZoneRepository;

class TimeZoneService extends BaseService
{
    public function __construct(TimeZoneRepository $timezone)
    {
        parent::__construct($timezone);
        $this->timezone = $timezone;
    }
}
