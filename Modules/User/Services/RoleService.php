<?php

namespace Modules\User\Services;

use Illuminate\Support\Facades\Log;
use Exception;
use Modules\User\Repositories\RoleRepository;

class RoleService
{
    protected $roles;

    public function __construct(RoleRepository $roles)
    {
        $this->roles = $roles;
    }

    public function listAll()
    {
        try {
            return $this->roles->getAllRoles();
        } catch (Exception $ex) {
            Log::error($ex->getMessage());
            throw new Exception('Failed to fetch roles');
        }
    }

    public function getUserRoles($userId)
    {
        try {
            return $this->roles->getRolesByUserId($userId);
        } catch (Exception $ex) {
            Log::error($ex->getMessage());
            return collect();
        }
    }

    public function getRoleNames($userId)
    {
        return $this->getUserRoles($userId)->pluck('name')->toArray();
    }

    public function hasRole($userId, $roleName)
    {
        return in_array($roleName, $this->getRoleNames($userId));
    }

    public function isAdmin($userId)
    {
        return $this->hasRole($userId, 'admin');
    }

    public function isBroker($userId)
    {
        return $this->hasRole($userId, 'broker');
    }

    public function getAdmins($search = null)
    {
        try {
            return $this->roles->getAllAdmins($search);
        } catch (Exception $ex) {
            Log::error($ex->getMessage());
            throw new Exception('Failed to fetch admins');
        }
    }

    public function getAllAdminsWithoutPagination($search = null)
    {
        try {
            return $this->roles->getAllAdminsWithoutPagination($search);
        } catch (Exception $ex) {
            Log::error($ex->getMessage());
            throw new Exception('Failed to fetch admins');
        }
    }

    /**
     * Admins who receive system notification emails (inquiries, listing approval, etc.).
     *
     * TODO: Restore DB lookup — return $this->getAllAdminsWithoutPagination($search) when ready.
     */
    public function getAdminNotificationRecipients()
    {
        return $this->mapNotificationEmailsToRecipients(
            config('constants.ADMIN_NOTIFICATION_EMAILS', [])
        );
    }

    /**
     * Admins who receive "listing sent for approval" notification emails.
     */
    public function getAdminSendForApprovalNotificationRecipients()
    {
        return $this->mapNotificationEmailsToRecipients(
            config('constants.ADMIN_SEND_FOR_APPROVAL_NOTIFICATION_EMAILS', [])
        );
    }

    private function mapNotificationEmailsToRecipients(array $emails)
    {
        $usersByEmail = $this->roles->findUsersByEmails($emails);

        return collect($emails)
            ->map(function (string $email) use ($usersByEmail) {
                $user = $usersByEmail->get($email);

                if ($user) {
                    return $user;
                }

                return (object) [
                    'id' => null,
                    'firstname' => 'Admin',
                    'lastname' => '',
                    'email' => $email,
                ];
            })
            ->values();
    }

    public function getBrokers($search = null, $page = 1, $perPage = 15)
    {
        try {
            return $this->roles->getAllBrokers($search, $page, $perPage);
        } catch (Exception $ex) {
            Log::error($ex->getMessage());
            throw new Exception('Failed to fetch brokers');
        }
    }

    public function getAllAdminsAndBrokers($search = null, $page = 1, $perPage = 15)
    {
        try {
            return $this->roles->getAllAdminsAndBrokers($search, $page, $perPage);
        } catch (Exception $ex) {
            Log::error($ex->getMessage());
            throw new Exception('Failed to fetch admins and brokers');
        }
    }


    public function addRole($userId, $roleName)
    {
        try {
            return $this->roles->attachRole($userId, $roleName);
        } catch (Exception $ex) {
            Log::error($ex->getMessage());
            throw new Exception("Failed to add role '{$roleName}': " . $ex->getMessage());
        }
    }

    public function removeRole($userId, $roleName)
    {
        try {
            return $this->roles->detachRole($userId, $roleName);
        } catch (Exception $ex) {
            Log::error($ex->getMessage());
            throw new Exception("Failed to remove role '{$roleName}': " . $ex->getMessage());
        }
    }

    public function addAdmin($userId)
    {
        return $this->addRole($userId, 'admin');
    }

    public function removeAdmin($userId)
    {
        return $this->removeRole($userId, 'admin');
    }

    public function addBroker($userId)
    {
        return $this->addRole($userId, 'broker');
    }

    public function removeBroker($userId)
    {
        return $this->removeRole($userId, 'broker');
    }
}
