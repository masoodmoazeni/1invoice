<?php

namespace Modules\Company\Services;


use App\Services\BaseService;
use Modules\Company\Repositories\BranchRepository;

class BranchService extends BaseService
{
    public function __construct(BranchRepository $branch)
    {
        parent::__construct($branch);
        $this->branch = $branch;
    }
}
