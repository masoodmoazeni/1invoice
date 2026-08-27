<?php

namespace Modules\Accounting\Repositories;

use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Model;
use Modules\Accounting\Entities\PaymentTerm;

class PaymentTermRepository extends BaseRepository
{
    protected Model $model;

    public function __construct(PaymentTerm $model)
    {
        $this->model = $model;
    }
}
