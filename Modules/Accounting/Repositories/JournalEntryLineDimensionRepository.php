<?php

namespace Modules\Accounting\Repositories;

use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Model;
use Modules\Accounting\Entities\JournalEntryLineDimension;

class JournalEntryLineDimensionRepository extends BaseRepository
{
    protected Model $model;

    public function __construct(JournalEntryLineDimension $model)
    {
        $this->model = $model;
    }
}
