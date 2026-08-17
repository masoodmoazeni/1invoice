<?php

namespace Modules\Accounting\Services;


use App\Services\BaseService;
use Modules\Accounting\Repositories\BranchRepository;

class BranchService extends BaseService
{
    public function __construct(BranchRepository $branch)
    {
        parent::__construct($branch);
        $this->branch = $branch;
    }
}
