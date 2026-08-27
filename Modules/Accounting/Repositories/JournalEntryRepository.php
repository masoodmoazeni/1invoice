<?php

namespace Modules\Accounting\Repositories;

use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Model;
use Modules\Accounting\Entities\JournalEntry;

class JournalEntryRepository extends BaseRepository
{
    protected Model $model;

    public function __construct(JournalEntry $model)
    {
        $this->model = $model;
    }
}
