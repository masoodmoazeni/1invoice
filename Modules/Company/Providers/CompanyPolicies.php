<?php

namespace Modules\Company\Providers;

use Illuminate\Support\Facades\Gate;
use Modules\Company\Entities\Branch;
use Modules\Company\Entities\Department;
use Modules\Company\Entities\FiscalYear;
use Modules\Company\Policies\BranchPolicy;
use Modules\Company\Policies\DepartmentPolicy;
use Modules\Company\Policies\FiscalYearPolicy;

// Register these policies inside CompanyServiceProvider::boot().
Gate::policy(Branch::class, BranchPolicy::class);
Gate::policy(Department::class, DepartmentPolicy::class);
Gate::policy(FiscalYear::class, FiscalYearPolicy::class);
