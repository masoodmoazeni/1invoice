<?php

namespace Modules\Accounting\Repositories;

use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Model;
use Modules\Accounting\Entities\AccountTemplates;

class AccountTemplateRepository extends BaseRepository
{
    protected Model $model;

    public function __construct(AccountTemplates $model)
    {
        $this->model = $model;
    }
}