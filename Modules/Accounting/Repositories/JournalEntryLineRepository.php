<?php

namespace Modules\Accounting\Repositories;

use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Model;
use Modules\Accounting\Entities\JournalEntrLines;

class JournalEntrLineRepository extends BaseRepository
{
    protected Model $model;

    public function __construct(JournalEntrLines $model)
    {
        $this->model = $model;
    }
}