<?php

namespace Modules\Accounting\Services;


use App\Services\BaseService;
use Modules\Accounting\Repositories\JournalEntryLineDimensionRepository;

class JournalEntryLineDimensionService extends BaseService
{
    public function __construct(JournalEntryLineDimensionRepository $journalEntryLineDimension)
    {
        parent::__construct($journalEntryLineDimension);
        $this->journalEntryLineDimension = $journalEntryLineDimension;
    }
}
