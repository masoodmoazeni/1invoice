<?php

namespace Modules\Accounting\Services;


use App\Services\BaseService;
use Modules\Accounting\Repositories\JournalEntryRepository;

class JournalEntryService extends BaseService
{
    public function __construct(JournalEntryRepository $journalentry)
    {
        parent::__construct($journalentry);
        $this->journalentry = $journalentry;
    }
}
