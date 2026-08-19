<?php

namespace Modules\Accounting\Services;


use App\Services\BaseService;
use Modules\Accounting\Repositories\PartnerRepository;

class PartnerService extends BaseService
{
    public function __construct(PartnerRepository $partner)
    {
        parent::__construct($partner);
        $this->partner = $partner;
    }
}
