<?php

namespace Modules\System\Services;


use App\Services\BaseService;
use Modules\System\Repositories\TimeZoneRepository;

class TimeZoneService extends BaseService
{
    public function __construct(TimeZoneRepository $timezone)
    {
        parent::__construct($timezone);
        $this->timezone = $timezone;
    }
}
