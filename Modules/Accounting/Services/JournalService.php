<?php

namespace Modules\Accounting\Services;


use App\Services\BaseService;
use Modules\Accounting\Repositories\JournalRepository;

class JournalService extends BaseService
{
    public function __construct(JournalRepository $journal)
    {
        parent::__construct($journal);
        $this->journal = $journal;
    }
}
