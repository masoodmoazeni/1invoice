<?php

namespace Modules\User\Services;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Modules\User\Repositories\UserRepository;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Modules\User\Entities\User;

class UserService
{
    protected $users;
    protected $emailService;

    public function __construct(
        UserRepository $userRepository,
        UserEmailService $emailService,
    ) {
        $this->users = $userRepository;
        $this->emailService = $emailService;
    }


    public function all(array $filters = [])
    {
        try {
            return $this->users->all($filters);
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            return collect();
        }
    }

    public function signup(array $data)
    {
        try {
            $userData = [
                'invited_id' => $data['invited_id'] ?? null,
                'firstname' => $data['firstname'] ?? null,
                'lastname'  => $data['lastname'] ?? null,
                'email'     => $data['email'],
                'mobile'    => $data['mobile'] ?? null,
                'active_email' => false,
                'verification_token' => Str::random(60),
                'google_id' => $data['google_id'] ?? null,
                'image' => $data['image'] ?? null,
            ];

            // Only hash password if provided
            if (isset($data['password']) && !empty($data['password'])) {
                $userData['password'] = Hash::make($data['password']);
            }

            $user = $this->users->signup($userData);

            if ($user instanceof User) {
                $this->emailService->sendSignupNotificationToAdmins($user);
            }

            return $user;
        } catch (Exception $ex) {
            Log::error($ex->getMessage());
            return $ex->getMessage();
        }
    }

    public function findByEmailAndToken(string $email, string $token)
    {
        try {
            $user = $this->users->findBy([
                'email' => $email,
                'verification_token' => $token
            ]);

            return $user;
        } catch (Exception $ex) {
            Log::error($ex->getMessage());
            return $ex->getMessage();
        }
    }


    public function findByEmail(string $email)
    {
        try {
            $user = $this->users->findBy([
                'email' => $email
            ]);

            return $user;
        } catch (Exception $ex) {
            Log::error($ex->getMessage());
            return $ex->getMessage();
        }
    }

    public function findByGoogleId(string $googleId)
    {
        try {
            $user = $this->users->findBy([
                'google_id' => $googleId
            ]);

            return $user;
        } catch (Exception $ex) {
            Log::error($ex->getMessage());
            return $ex->getMessage();
        }
    }

    public function find($id)
    {
        try {
            $user = $this->users->findBy([
                'id' => $id
            ]);

            return $user;
        } catch (Exception $ex) {
            Log::error($ex->getMessage());
            return $ex->getMessage();
        }
    }

    public function show($id)
    {
        try {
            $user = $this->users->findById($id);

            if (!$user) {
                return null;
            }

            return $user;
        } catch (Exception $ex) {
            Log::error($ex->getMessage());
            return $ex->getMessage();
        }
    }

    public function findByToken(string $token)
    {
        try {
            $user = $this->users->findBy([
                'verification_token' => $token
            ]);

            return $user;
        } catch (Exception $ex) {
            Log::error($ex->getMessage());
            return $ex->getMessage();
        }
    }

    public function forgetpassword(string $email)
    {

        try {
            $user = $this->findByEmail($email);
            if ($user) {
                $token = Str::random(60);
                $this->users->updateToken($user->id, $token);
                $this->emailService->resetPasswordAndSendEmailToken($user, $token);

                return $token;
            }
        } catch (Exception $ex) {
            Log::error($ex->getMessage());
            return $ex->getMessage();
        }
    }


    public function acceptInvitation(int $id, string $password)
    {
        try {
            $user = $this->users->findById($id);

            if ($user) {
                $acceptedUser = $this->users->acceptInvitation($user->id, $password);
                return $acceptedUser;
            }

            return null;
        } catch (Exception $ex) {
            Log::error($ex->getMessage());
            throw $ex;
        }
    }

    public function uploadProfileImage($userId, $image)
    {
        return $this->users->uploadProfileImage($userId, $image);
    }

    public function getPagination(
        $perPage = 15,
        $search = null,
        $id = null,
        $invited_id = null,
        $role = null,
        $status = null,
        bool $includeBrokerLinkedUsers = false
    ) {
        try {
            return $this->users->paginateWithSearch(
                $perPage,
                $search,
                $id,
                $invited_id,
                $role,
                $status,
                $includeBrokerLinkedUsers
            );
        } catch (Exception $ex) {
            Log::error($ex->getMessage());
            return $ex->getMessage();
        }
    }

    public function update($id, array $data)
    {
        try {
            $user = $this->users->update($id, $data);

            if (!$user) {
                return null;
            }

            return $user;
        } catch (Exception $ex) {
            Log::error($ex->getMessage());
            return $ex->getMessage();
        }
    }

    public function deleteImage($id)
    {
        try {
            $user = $this->users->deleteImage($id);

            if (!$user) {
                return null;
            }

            return $user;
        } catch (Exception $ex) {
            Log::error($ex->getMessage());
            return $ex->getMessage();
        }
    }

    public function delete($id)
    {
        try {
            if (!User::query()->whereKey($id)->exists()) {
                return false;
            }

            DB::transaction(function () use ($id) {
                $deleted = $this->users->delete($id);
                if (!$deleted) {
                    throw new Exception('User could not be deleted');
                }
            });
        } catch (Exception $ex) {
            Log::error($ex->getMessage());
            return $ex->getMessage();
        }
    }

    public function getBrokersAndAdmins($perPage = 15, $search = null)
    {
        try {
            return $this->users->paginateBrokersAndAdmins($perPage, $search);
        } catch (Exception $ex) {
            Log::error($ex->getMessage());
            return $ex->getMessage();
        }
    }

    // Update Password
    public function updatePassword(int $id, string $password)
    {
        try {
            $user = $this->users->updatePassword($id, $password);

            if (!$user) {
                return null;
            }

            return $user;
        } catch (Exception $ex) {
            Log::error($ex->getMessage());
            return $ex->getMessage();
        }
    }

    /**
     * Resend the invitation email for users who have not activated their email yet.
     * Rotates the verification token only after the message is accepted for delivery.
     */
    public function resendInvitationEmail(int $id): User
    {
        $user = User::query()
            ->with(['roles:id,name,label'])
            ->find($id);

        if (!$user) {
            throw new Exception('User not found');
        }

        if ($user->active_email) {
            throw new Exception('This user has already activated their email.');
        }

        $token = Str::random(60);
        $user->verification_token = $token;

        $invitationPortal = $user->getUserType() === 'admin' ? 'admin' : null;

        $sent = $this->emailService->sendInvitationEmail($user, $invitationPortal);

        if (!$sent) {
            throw new Exception('Failed to send invitation email.');
        }

        $this->users->updateToken($user->id, $token);

        return $user->fresh(['roles:id,name,label']);
    }
}
