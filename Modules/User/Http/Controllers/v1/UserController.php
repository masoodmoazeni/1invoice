<?php

namespace Modules\User\Http\Controllers\v1;

use App\Http\Controllers\BaseController;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
use Modules\User\Entities\User;
use Modules\User\Http\Requests\AcceptInvitationRequest;
use Modules\User\Http\Requests\ConfirmPasswordRequest;
use Modules\User\Http\Requests\ForgetPasswordRequest;
use Modules\User\Http\Requests\ForgetPasswordVerifiedRequest;
use Modules\User\Http\Requests\SigninRequest;
use Modules\User\Http\Requests\SignupRequest;
use Modules\User\Http\Requests\VerifiedEmailRequest;
use Modules\User\Services\RoleService;
use Modules\User\Services\UserEmailService;
use Modules\User\Services\UserService;
use Tymon\JWTAuth\Facades\JWTAuth;

/**
 * @OA\Tag(
 *     name="User",
 *     description="Operations about users"
 * )
 */
class UserController extends BaseController
{

    protected $users;
    protected $roleService;
    protected $emailService;

    public function __construct(UserService $userService, RoleService $roleService, UserEmailService $emailService)
    {
        $this->users = $userService;
        $this->roleService = $roleService;
        $this->emailService = $emailService;
    }

    /**
     * @OA\Post(
     *     path="/users/signup",
     *     summary="register user",
     *     tags={"User"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email","password"},
     *               @OA\Property(property="firstname", type="string"),
     *               @OA\Property(property="lastname", type="string"),
     *               @OA\Property(property="email", type="string", format="email"),
     *               @OA\Property(property="mobile", type="string"),
     *               @OA\Property(property="password", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="register one of users"
     *     )
     * )
     */
    public function signup(SignupRequest $request)
    {
        try {
            $data = $request->validated();
            $user = $this->users->signup($data);

            if (!$user instanceof User) {
                return $this->errorResponse(is_string($user) ? $user : 'Failed to create user.');
            }

            $this->emailService->sendVerificationEmail($user, $data['redirect_url'] ?? null);

            $export = [
                'id' => $user->id,
                'firstname' => $user->firstname,
                'lastname' => $user->lastname,
                'email' => $user->email,
                'mobile' => $user->mobile,
                'verification_token' => $user->verification_token,
            ];

            return $this->successResponse($export);
        } catch (ValidationException $ex) {
            throw $ex;
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            return $this->errorResponse($ex->getMessage());
        }
    }

    /**
     * @OA\Post(
     *     path="/users/signup/test",
     *     summary="register user",
     *     tags={"User"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email"},
     *               @OA\Property(property="email", type="string", format="email"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="register one of users"
     *     )
     * )
     */
    public function signupTest(Request $request)
    {
        try {


            $user = $this->emailService->send();
            return $this->successResponse($user);
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            return $this->errorResponse($ex->getMessage());
        }
    }

    /**
     * @OA\Post(
     *     path="/users/verifiedemail",
     *     summary="verifiedemail one of user",
     *     tags={"User"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"token"},
     *               @OA\Property(property="token", type="string"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="verified one of users"
     *     )
     * )
     */
    public function verifiedEmail(VerifiedEmailRequest $request)
    {
        try {
            $user = User::where('verification_token', $request->token)->first();


            if (!$user) {
                return $this->errorResponse("The verification token is invalid or expired.");
            }

            $user->active_email = true;
            $user->verification_token = null;
            $user->save();


            $token = JWTAuth::fromUser($user);

            return response()->json([
                'message' => 'Your email has been successfully verified. You can now log in..',
                'access_token' => $token,
                'user' => $user
            ]);
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            return $this->errorResponse($ex->getMessage());
        }
    }

    /**
     * @OA\Post(
     *     path="/users/accept-invitation",
     *     summary="Accept invitation",
     *     tags={"User"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"token","password"},
     *               @OA\Property(property="token", type="string"),
     *               @OA\Property(property="password", type="string"),
     *               @OA\Property(property="password_confirmation", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Invitation accepted successfully"
     *     )
     * )
     */
    public function acceptInvitation(AcceptInvitationRequest $request)
    {
        try {
            $user = User::where('verification_token', $request->token)->first();

            if (!$user) {
                return $this->errorResponse("The invitation token is invalid or expired.");
            }

            // Set password and clear token and active email
            $acceptedUser = $this->users->acceptInvitation($user->id, $request->password);
            if (!$acceptedUser) {
                return $this->errorResponse("Failed to activate invited user.");
            }

            $acceptedUser->load('roles');
            $invitationPortal = $acceptedUser->hasRole('admin') ? 'admin' : null;

            return $this->successResponse([
                'id' => $acceptedUser->id,
                'email' => $acceptedUser->email,
                'active_email' => (int) $acceptedUser->active_email,
                'status' => (int) $acceptedUser->status,
                'portal' => $invitationPortal,
            ], "Invitation accepted successfully. You can login now.");
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            return $this->errorResponse($ex->getMessage());
        }
    }

    /**
     * @OA\Post(
     *     path="/users/signin",
     *     summary="signin user",
     *     tags={"User"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email","password"},
     *               @OA\Property(property="email", type="string", format="email"),
     *               @OA\Property(property="password", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="sign in and get token"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function signin(SigninRequest $request)
    {

        try {
            $user = $this->users->findByEmail($request->email);

            if (!$user) {
                return $this->errorResponse("User not found.", 404);
            }

            if ($user->active_email == false) {
                return $this->errorResponse("Please Confirm your email first.", 403);
            }

            $credentials = $request->only(['email', 'password']);

            if (!$token = auth()->attempt($credentials)) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $roles = $this->roleService->getUserRoles($user->id);
            if (!$roles->isEmpty()) {
                $user['broker'] = $this->roleService->isBroker($user->id);
                $user['admin']  = $this->roleService->isAdmin($user->id);
            }

            return response()->json([
                'access_token' => $token,
                'token_type'   => 'bearer',
                'expires_in'   => auth()->factory()->getTTL() * 60,
                'user'         => $user
            ]);
        } catch (Exception $ex) {
            Log::error($ex->getMessage());
            return $this->errorResponse($ex->getMessage());
        }
    }

    /**
     * @OA\Post(
     *     path="/users/forgetpassword",
     *     summary="forget password",
     *     tags={"User"},
     *     @OA\RequestBody(
     *        required=true,
     *         @OA\JsonContent(
     *             required={"email"},
     *             @OA\Property(property="email", type="string", format="email", example="user@example.com")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Password reset link sent successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="password changed failed"
     *     )
     * )
     */
    public function forgetpassword(ForgetPasswordRequest $request)
    {
        try {

            $user = $this->users->findByEmail($request->email);

            if (!$user) {
                return $this->errorResponse("User not found.", 404);
            }

            $forget = $this->users->forgetpassword($request->email);

            return $this->successResponse($forget);
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            return $this->errorResponse($ex->getMessage());
        }
    }

    /**
     * @OA\Post(
     *     path="/users/forgetpassword/verified",
     *     summary="forget password verified",
     *     tags={"User"},
     *     @OA\RequestBody(
     *        required=true,
     *         @OA\JsonContent(
     *             required={"email","token","password"},
     *             @OA\Property(property="email", type="string", format="email", example="user@example.com"),
     *             @OA\Property(property="token", type="string"),
     *             @OA\Property(property="password", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Password change successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="password changed failed"
     *     )
     * )
     */
    public function ForgetPasswordVerified(ForgetPasswordVerifiedRequest $request)
    {
        try {

            $email = $this->users->findByEmail($request->email);
            if (!$email) {
                return $this->errorResponse("User not found.", 404);
            }

            $token = $this->users->findByToken($request->token);
            if (!$token) {
                return $this->errorResponse("Token not found.", 404);
            }

            $user = $this->users->findByEmailAndToken($request->email, $request->token);

            $user = $this->users->updatePassword($user->id, $request->password);

            return $this->successResponse($user);
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            return $this->errorResponse($ex->getMessage());
        }
    }
}
