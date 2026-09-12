<?php

namespace App\Traits;

trait RolePermissionTrait
{
    protected function getCurrentUserRole()
    {
        $user = auth()->user();
        return $user?->roles->pluck('name')->toArray();

    }

//    protected function isSuperAdmin()
//    {
//        $user = auth()->user();
//        if (!$user) return false;
//
//        return $user->roles->contains('name', 'super_admin');
//    }

//    protected function isAminMakhzan()
//    {
//        $user = auth()->user();
//        if (!$user) return false;
//
//        return $user->roles->contains('name', 'amin_makhzan');
//    }
//
//    protected function isUser()
//    {
//        $user = auth()->user();
//        if (!$user) return false;
//
//        return $user->roles->contains('name', 'user');
//    }
}
