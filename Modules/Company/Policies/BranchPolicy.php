<?php

namespace Modules\Company\Policies;

use App\Models\User;
use Modules\Company\Entities\Branch;

class BranchPolicy
{
    public function viewAny(User $user): bool { return true; }
    public function view(User $user, Branch $branch): bool { return true; }
    public function create(User $user): bool { return true; }
    public function update(User $user, Branch $branch): bool { return true; }
    public function delete(User $user, Branch $branch): bool { return true; }
}
