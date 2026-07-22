<?php

namespace Modules\User\Repositories;

use Modules\User\Entities\Role;
use Modules\User\Entities\User;

class RoleRepository
{
    protected $model;

    public function __construct(Role $model)
    {
        $this->model = $model;
    }

    public function getAllRoles()
    {
        return $this->model->select('id', 'name', 'label')->get();
    }

    public function getRolesByUserId($userId)
    {
        return $this->model
            ->whereHas('users', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->select('id', 'name', 'label')
            ->get();
    }

    public function getAllAdmins($search = null)
    {
        return $this->getUsersByRole('admin', $search);
    }

    public function findUsersByEmails(array $emails)
    {
        if (empty($emails)) {
            return collect();
        }

        return User::whereIn('email', $emails)
            ->select('id', 'firstname', 'lastname', 'email', 'mobile', 'image')
            ->get()
            ->keyBy('email');
    }

    public function getAllAdminsWithoutPagination($search = null)
    {
        $query = User::whereHas('roles', function ($query) {
            $query->where('name', 'admin');
        })->select('id', 'firstname', 'lastname', 'email', 'mobile', 'image');

        if (!empty($search)) {
            $search = strtolower($search);
            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(firstname) LIKE ?', ["%{$search}%"])
                    ->orWhereRaw('LOWER(lastname) LIKE ?', ["%{$search}%"])
                    ->orWhereRaw('LOWER(mobile) LIKE ?', ["%{$search}%"])
                    ->orWhereRaw('LOWER(email) LIKE ?', ["%{$search}%"]);
            });
        }

        return $query->get();
    }

    public function getAllBrokers($search = null, $page = 1, $perPage = 15)
    {
        return $this->getUsersByRole('broker', $search, $page, $perPage);
    }

    public function attachRole($userId, $roleName)
    {
        $role = $this->model->where('name', $roleName)->first();
        if (!$role) {
            throw new \Exception("Role '{$roleName}' not found");
        }

        $user = User::findOrFail($userId);
        $user->roles()->syncWithoutDetaching([$role->id]);

        return $user->roles;
    }

    public function detachRole($userId, $roleName)
    {
        $role = $this->model->where('name', $roleName)->first();
        if (!$role) {
            throw new \Exception("Role '{$roleName}' not found");
        }

        $user = User::findOrFail($userId);
        $user->roles()->detach($role->id);

        return $user->roles;
    }

    /**
     * دریافت کاربران بر اساس نقش + جستجو
     */
    protected function getUsersByRole($roleName, $search = null, $page = 1, $perPage = 15)
    {
        $query = User::whereHas('roles', function ($query) use ($roleName) {
            $query->where('name', $roleName);
        })
            ->select('id', 'firstname', 'lastname', 'email', 'mobile', 'image');

        if (!empty($search)) {
            $search = strtolower($search);

            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(firstname) LIKE ?', ["%{$search}%"])
                    ->orWhereRaw('LOWER(lastname) LIKE ?', ["%{$search}%"])
                    ->orWhereRaw('LOWER(mobile) LIKE ?', ["%{$search}%"])
                    ->orWhereRaw('LOWER(email) LIKE ?', ["%{$search}%"]);
            });
        }
        // صفحه‌بندی
        return $query->paginate($perPage, ['*'], 'page', $page);
    }

    public function getAllAdminsAndBrokers($search = null, $page = 1, $perPage = 15)
    {
        $query = User::whereHas('roles', function ($query) {
            $query->whereIn('name', ['admin', 'broker']);
        })
            ->select('id', 'firstname', 'lastname', 'email', 'mobile', 'image');

        if (!empty($search)) {
            $search = strtolower($search);

            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(firstname) LIKE ?', ["%{$search}%"])
                    ->orWhereRaw('LOWER(lastname) LIKE ?', ["%{$search}%"])
                    ->orWhereRaw('LOWER(mobile) LIKE ?', ["%{$search}%"])
                    ->orWhereRaw('LOWER(email) LIKE ?', ["%{$search}%"]);
            });
        }

        return $query->paginate($perPage, ['*'], 'page', $page);
    }
}
