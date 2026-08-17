<?php

namespace Modules\Accounting\Services;


use App\Services\BaseService;
use Modules\Accounting\Repositories\JournalEntryLineRepository;

class JournalEntryLineService extends BaseService
{
    public function __construct(JournalEntryLineRepository $journalentryline)
    {
        parent::__construct($journalentryline);
        $this->journalentryline = $journalentryline;
    }
}
