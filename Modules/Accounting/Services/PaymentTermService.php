<?php

namespace Modules\Accounting\Services;


use App\Services\BaseService;
use Modules\Accounting\Repositories\PaymentTermRepository;

class PaymentTermService extends BaseService
{
    public function __construct(PaymentTermRepository $paymentTerm)
    {
        parent::__construct($paymentTerm);
        $this->paymentTerm = $paymentTerm;
    }
}
