<?php

namespace Modules\User\Services;

use Exception;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Modules\User\Repositories\UserEmailRepository;

class UserEmailService
{
    protected $usersEmailRepository;

    public function __construct(UserEmailRepository $usersEmailRepository)
    {
        $this->usersEmailRepository = $usersEmailRepository;
    }

    public function sendVerificationEmail($user, $redirectUrl = null)
    {
        $this->usersEmailRepository->sendVerificationEmail($user, $redirectUrl);
    }

    public function sendSignupNotificationToAdmins($user)
    {
        return $this->usersEmailRepository->sendSignupNotificationToAdmins($user);
    }

    public function sendInvitationEmail($user, ?string $portal = null): bool
    {
        return $this->usersEmailRepository->sendInvitationEmail($user, $portal);
    }

    public function resetPasswordAndSendEmailToken($user, $token)
    {
        $this->usersEmailRepository->resetPasswordAndSendEmailToken($user, $token);
    }


    public function send()
    {
        $this->usersEmailRepository->send();
    }



    public function getPagination($perPage = 15, $search = null)
    {
        try {
            return $this->usersEmailRepository->paginateWithSearch($perPage, $search);
        } catch (Exception $ex) {
            Log::error($ex->getMessage());
        }
    }
}
