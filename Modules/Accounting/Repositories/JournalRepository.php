<?php

namespace Modules\Accounting\Repositories;

use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Model;
use Modules\Accounting\Entities\Journals;

class JournalRepository extends BaseRepository
{
    protected Model $model;

    public function __construct(Journals $model)
    {
        $this->model = $model;
    }
}