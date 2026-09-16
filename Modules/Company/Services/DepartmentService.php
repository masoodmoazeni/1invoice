<?php

namespace Modules\Company\Services;


use App\Services\BaseService;
use Modules\Company\Repositories\DepartmentRepository;

class DepartmentService extends BaseService
{
    public function __construct(DepartmentRepository $department)
    {
        parent::__construct($department);
        $this->department = $department;
    }
}
