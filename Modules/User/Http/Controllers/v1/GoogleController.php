<?php

namespace Modules\User\Http\Controllers\v1;

use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\Http;
use Modules\User\Services\UserService;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Log;
use Modules\User\Services\UserEmailService;

/**
 * @OA\Tag(
 *     name="Authentication",
 *     description="Google Authentication APIs"
 * )
 */
class GoogleController extends BaseController
{
    protected $userService;
    protected $emailService;

    public function __construct(UserService $userService, UserEmailService $emailService)
    {
        $this->userService = $userService;
        $this->emailService = $emailService;
    }

    /**
     * @OA\Post(
     *     path="/users/login/google/token",
     *     summary="Authenticate with Google ID Token",
     *     tags={"User"},
     *
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"id_token"},
     *             @OA\Property(
     *                 property="id_token",
     *                 type="string",
     *                 description="Google ID Token received from Google Sign-In SDK",
     *                 example="eyJhbGciOiJSUzI1NiIsInR5cCI6IkpXVCJ9..."
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Authentication successful"
     *     )
     * )
     */
    public function handleGoogleCallback(Request $request)
    {
        $request->validate([
            'id_token' => 'required|string',
        ]);

        try {
            // 1. Verify ID Token with Google
            $response = Http::get('https://oauth2.googleapis.com/tokeninfo', [
                'id_token' => $request->id_token,
            ]);

            if (!$response->ok()) {
                return $this->errorResponse('Invalid Google ID token', 401);
            }

            $googleData = $response->json();

            // 2. Extract data
            $googleId = $googleData['sub'];
            $email    = $googleData['email'] ?? null;
            $name     = $googleData['name'] ?? null;
            $avatar   = $googleData['picture'] ?? null;

            // 3. Find user by google_id
            $user = $this->userService->findByEmail($email);

            // 4. Create user if not exists
            if (!$user) {
                $random = Rand(1, 10000000);
                $data = [
                    'google_id'         => $googleId,
                    'firstname'         => $name,
                    'email'             => $email,
                    'active_email'      => 1,
                    'status'            => 1,
                    'image'             => $avatar,
                    'password'          => $random,
                    'email_verified_at' => now(),
                ];

                $user = $this->userService->signup($data);
            }

            $token = auth()->login($user);

            return response()->json([
                'access_token' => $token,
                'token_type'   => 'bearer',
                'expires_in'   => auth()->factory()->getTTL() * 60,
                'user'         => $user
            ]);
        } catch (Exception $ex) {
            Log::error($ex->getMessage());
            return $this->errorResponse('Unable to authenticate with Google', 400);
        }
    }
}
