<?php

namespace Modules\Accounting\Services;


use App\Services\BaseService;
use Modules\Accounting\Repositories\NumberSequenceRepository;

class NumberSequenceService extends BaseService
{
    public function __construct(NumberSequenceRepository $numberSequence)
    {
        parent::__construct($numberSequence);
        $this->numberSequence = $numberSequence;
    }
}
