<?php

namespace Modules\Company\Policies;

use App\Models\User;
use Modules\Company\Entities\FiscalYear;

class FiscalYearPolicy
{
    public function viewAny(User $user): bool { return true; }
    public function view(User $user, FiscalYear $fiscalYear): bool { return true; }
    public function create(User $user): bool { return true; }
    public function update(User $user, FiscalYear $fiscalYear): bool { return true; }
    public function delete(User $user, FiscalYear $fiscalYear): bool { return true; }
}
