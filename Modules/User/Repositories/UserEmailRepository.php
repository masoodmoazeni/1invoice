<?php

namespace Modules\User\Repositories;

use Exception;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Modules\User\Entities\UserEmail;
use Modules\User\Services\RoleService;

class UserEmailRepository
{
    protected $model;
    protected $roleService;

    public function __construct(UserEmail $model, RoleService $roleService)
    {
        $this->model = $model;
        $this->roleService = $roleService;
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function findByUserId($userId, $type = null)
    {
        $query = $this->model->where('user_id', $userId);

        if (!is_null($type)) {
            $query->where('type', $type);
        }

        return $query->get();
    }

    public function deleteByUserId($userId)
    {
        return $this->model->where('user_id', $userId)->delete();
    }


    public function paginateWithSearch($perPage = 15, $search = null)
    {
        $query = $this->model->with('user');

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('type', $search);
            });
        }

        return $query->orderByDesc('id')->paginate($perPage);
    }

    public function sendVerificationEmail($user, $redirectUrl = null)
    {
        try {
            $frontendUrl = config('app.frontend_url');
            $verificationUrl = $frontendUrl . '/verify-email?token=' . $user->verification_token;

            if ($redirectUrl) {
                $verificationUrl .= '&redirect_url=' . urlencode($redirectUrl);
            }

            $subject = "Welcome to Salonspa Connection- Confirm Your Email Address";

            $htmlContent = view('emails.UserConfirmEmailAddress', [
                'first_name'               => $user->firstname . ' ' . $user->lastname,
                'confirmation_link'        => $verificationUrl
            ])->render();

            $email = new \SendGrid\Mail\Mail();
            $email->setFrom(config('services.sendgrid.from_email'), config('services.sendgrid.from_name'));
            $email->setSubject($subject);
            $email->addTo($user->email, $user->firstname . ' ' . $user->lastname);
            $email->addContent('text/html', $htmlContent);

            $emailUuid = (string) Str::uuid();
            $email->addCustomArg('app_email_id', $emailUuid);
            $email->addCustomArg('email_type', 'user_confirm_email_address');
            $email->addCustomArg('user_id', (string) $user->id);

            $sendgridApi = config('services.sendgrid.key');
            $sendgrid = new \SendGrid($sendgridApi);

            $response = $sendgrid->send($email);

            if (isset($this->model)) {
                $this->model->create([
                    'user_id'     => $user->id,
                    'title'       => $subject,
                    'description' => $htmlContent,
                    'type'        => 'user_confirm_email_address',
                    'email_uuid'  => $emailUuid,
                    'status'      => 'sent',
                ]);
            }


            Log::info("UserConfirmEmailAddress SendGrid email sent to UserConfirmEmailAddress", [
                'UserConfirmEmailAddress_id'     => $user->id,
                'status'                         => $response->statusCode()
            ]);

            return true;
        } catch (\Exception $e) {

            Log::error("SendGrid UserConfirmEmailAddress email error: " . $e->getMessage());
            return false;
        }
    }

    public function sendSignupNotificationToAdmins($user)
    {
        try {
            $admins = $this->roleService->getAdminNotificationRecipients();

            if ($admins->isEmpty()) {
                Log::warning('No admins found to send user signup notification');
                return false;
            }

            $subject = 'New User Registration';

            foreach ($admins as $admin) {
                try {
                    $htmlContent = view('emails.UserSignupToAdmin', [
                        'first_name' => $user->firstname,
                        'last_name' => $user->lastname,
                        'email' => $user->email,
                        'mobile' => $user->mobile,
                        'user_id' => $user->id,
                    ])->render();

                    $email = new \SendGrid\Mail\Mail();
                    $email->setFrom(config('services.sendgrid.from_email'), config('services.sendgrid.from_name'));
                    $email->setSubject($subject);
                    $email->addTo($admin->email, $admin->firstname . ' ' . $admin->lastname);
                    $email->addContent('text/html', $htmlContent);

                    $emailUuid = (string) Str::uuid();
                    $email->addCustomArg('app_email_id', $emailUuid);
                    $email->addCustomArg('email_type', 'user_signup_to_admin');
                    if (!empty($admin->id)) {
                        $email->addCustomArg('admin_id', (string) $admin->id);
                    }
                    $email->addCustomArg('user_id', (string) $user->id);

                    $sendgrid = new \SendGrid(config('services.sendgrid.key'));
                    $response = $sendgrid->send($email);
                    $statusCode = $response->statusCode();

                    if ($statusCode >= 200 && $statusCode < 300) {
                        Log::info('User signup notification sent to admin', [
                            'admin_id' => $admin->id,
                            'user_id' => $user->id,
                            'to_email' => $admin->email,
                            'status_code' => $statusCode,
                        ]);
                    } else {
                        Log::error('Failed to send user signup notification to admin', [
                            'admin_id' => $admin->id,
                            'user_id' => $user->id,
                            'to_email' => $admin->email,
                            'status_code' => $statusCode,
                            'response_body' => $response->body(),
                        ]);
                    }
                } catch (\Exception $e) {
                    Log::error('Error sending user signup notification to admin', [
                        'admin_id' => $admin->id ?? null,
                        'user_id' => $user->id,
                        'to_email' => $admin->email ?? null,
                        'error_message' => $e->getMessage(),
                    ]);
                }
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Error sending user signup notifications to admins: ' . $e->getMessage());
            return false;
        }
    }

    public function sendInvitationEmail($user, ?string $portal = null)
    {
        try {
            $frontendUrl = config('app.frontend_url');
            $query = ['token' => $user->verification_token];
            if ($portal !== null && $portal !== '') {
                $query['portal'] = $portal;
            }
            $invitationUrl = $frontendUrl . '/verify-invitation-email?' . http_build_query($query);

            $subject = "You're Invited to Join Salonspa Connection";

            $htmlContent = view('emails.UserInvitation', [
                'first_name'       => $user->firstname . ' ' . $user->lastname,
                'invitation_link'  => $invitationUrl
            ])->render();

            $email = new \SendGrid\Mail\Mail();
            $email->setFrom(config('services.sendgrid.from_email'), config('services.sendgrid.from_name'));
            $email->setSubject($subject);
            $email->addTo($user->email, $user->firstname . ' ' . $user->lastname);
            $email->addContent('text/html', $htmlContent);

            $emailUuid = (string) Str::uuid();
            $email->addCustomArg('app_email_id', $emailUuid);
            $email->addCustomArg('email_type', 'user_invitation');
            $email->addCustomArg('user_id', (string) $user->id);

            $sendgridApi = config('services.sendgrid.key');
            $sendgrid = new \SendGrid($sendgridApi);

            $response = $sendgrid->send($email);

            if (isset($this->model)) {
                $this->model->create([
                    'user_id'     => $user->id,
                    'title'       => $subject,
                    'description' => $htmlContent,
                    'type'        => 'user_invitation',
                    'email_uuid'  => $emailUuid,
                    'status'      => 'sent',
                ]);
            }

            Log::info("UserInvitation SendGrid email sent", [
                'user_id'     => $user->id,
                'status'      => $response->statusCode()
            ]);

            return true;
        } catch (\Exception $e) {

            Log::error("SendGrid UserInvitation email error: " . $e->getMessage());
            return false;
        }
    }

    public function resetPasswordAndSendEmailToken($user, $token)
    {
        try {

            $frontendUrl     = config('app.frontend_url');
            $verificationUrl = $frontendUrl . '/change-password?token=' . $token;

            $subject = "Welcome to Salonspa Connection- Reset Your Password";

            $htmlContent = view('emails.UserResetPassword', [
                'first_name'        => $user->firstname . ' ' . $user->lastname,
                'confirmation_link' => $verificationUrl
            ])->render();

            $email = new \SendGrid\Mail\Mail();
            $email->setFrom(config('services.sendgrid.from_email'), config('services.sendgrid.from_name'));
            $email->setSubject($subject);
            $email->addTo($user->email, $user->firstname . ' ' . $user->lastname);
            $email->addContent('text/html', $htmlContent);

            $emailUuid = (string) Str::uuid();
            $email->addCustomArg('app_email_id', $emailUuid);
            $email->addCustomArg('email_type', 'reset_password');
            $email->addCustomArg('user_id', (string) $user->id);

            $sendgridApi = config('services.sendgrid.key');
            $sendgrid    = new \SendGrid($sendgridApi);

            $response = $sendgrid->send($email);

            if (isset($this->model)) {
                $this->model->create([
                    'user_id'     => $user->id,
                    'title'       => $subject,
                    'description' => $htmlContent,
                    'type'        => 'reset_password',
                    'email_uuid'  => $emailUuid,
                    'status'      => 'sent',
                ]);
            }

            Log::info("ResetPassword email sent", [
                'user_id'    => $user->id,
                'email_uuid' => $emailUuid,
                'status'    => $response->statusCode(),
            ]);

            return true;
        } catch (\Exception $e) {

            Log::error("SendGrid ResetPassword email error", [
                'user_id' => $user->id ?? null,
                'error'   => $e->getMessage()
            ]);

            return false;
        }
    }

    public function send()
    {
        $email = new \SendGrid\Mail\Mail();
        // Replace the email address and name with your verified sender
        $email->setFrom(
            'website@salonspaconnection.com',
            'SalonSpa Connection '
        );
        $email->setSubject('Sending with Twilio SendGrid is Fun');
        // Replace the email address and name with your recipient
        $email->addTo(
            'm.moazeni68@gmail.com',
            'Sayed Ashraf'
        );
        $email->addContent(
            'text/html',
            view('emails.template', [
                'firstname' => 'This is a test email Salam Haji',
                'action_url' => 'This is a test email Salam Haji',
            ])->render(),
        );

        $sendgridApi = config('services.sendgrid.key');
        $sendgrid = new \SendGrid($sendgridApi);

        try {
            $response = $sendgrid->send($email);
            printf("Response status: %d\n\n", $response->statusCode());

            $headers = array_filter($response->headers());
            echo "Response Headers\n\n";
            foreach ($headers as $header) {
                echo '- ' . $header . "\n";
            }
        } catch (Exception $e) {
            echo 'Caught exception: ' . $e->getMessage() . "\n";
        }
    }
}
