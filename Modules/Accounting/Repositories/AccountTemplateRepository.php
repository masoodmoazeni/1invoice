<?php

namespace Modules\Accounting\Repositories;

use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Model;
use Modules\Accounting\Entities\AccountTemplate;

class AccountTemplateRepository extends BaseRepository
{
    protected Model $model;

    public function __construct(AccountTemplate $model)
    {
        $this->model = $model;
    }
}
