<?php

namespace Modules\Accounting\Repositories;

use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Model;
use Modules\Accounting\Entities\JournalEntryLine;

class JournalEntryLineRepository extends BaseRepository
{
    protected Model $model;

    public function __construct(JournalEntryLine $model)
    {
        $this->model = $model;
    }
}
