<?php

namespace Modules\Company\Policies;

use App\Models\User;
use Modules\Company\Entities\Department;

class DepartmentPolicy
{
    public function viewAny(User $user): bool { return true; }
    public function view(User $user, Department $department): bool { return true; }
    public function create(User $user): bool { return true; }
    public function update(User $user, Department $department): bool { return true; }
    public function delete(User $user, Department $department): bool { return true; }
}
