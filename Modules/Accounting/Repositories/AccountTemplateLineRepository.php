<?php

namespace Modules\Accounting\Repositories;

use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Model;
use Modules\Accounting\Entities\AccountTemplateLine;

class AccountTemplateLineRepository extends BaseRepository
{
    protected Model $model;

    public function __construct(AccountTemplateLine $model)
    {
        $this->model = $model;
    }
}
