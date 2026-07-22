<?php

namespace Modules\User\Http\Controllers\v1;

use App\Http\Controllers\BaseController;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Modules\User\Http\Requests\ChangePasswordRequest;
use Modules\User\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;
use Modules\User\Http\Requests\ChangeProfileRequest;
use Modules\User\Repositories\UserEmailRepository;
use Modules\User\Services\UserService;

class ProfileController extends BaseController
{
    protected $users;
    protected $emails;

    public function __construct(UserService $userService, UserEmailRepository $userEmailRepository)
    {
        $this->users = $userService;
        $this->emails = $userEmailRepository;
    }
    /**
     * @OA\Get(
     *     path="/users/profile/data",
     *     summary="show profile user",
     *     tags={"Profile"},
     *     security={{"jwt":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="show profile successfully"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="profile failed"
     *     )
     * )
     */
    public function index()
    {
        try {
            $user = Auth::user();
            return $this->successResponse($user);
        } catch (Exception $ex) {
            Log::error($ex->getMessage());
            $this->errorResponse($ex->getMessage());
        }
    }

    /**
     * @OA\Post(
     *     path="/users/profile/changepassword",
     *     summary="Change user password",
     *     tags={"Profile"},
     *     security={{"jwt":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"password"},
     *             @OA\Property(property="password", type="string", format="password"),
     *             @OA\Property(property="current_password", type="string", format="password")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Password changed successfully"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Change password failed"
     *     )
     * )
     */
    public function changepassword(ChangePasswordRequest $request)
    {
        try {
            $user = Auth::user();


            if ($request->filled('current_password')) {
                if (!Hash::check($request->current_password, $user->password)) {
                    return $this->errorResponse("Current password is incorrect.", 422);
                }
            }

            $this->users->updatePassword($user->id, $request->password);
            return $this->successResponse($user);
        } catch (Exception $ex) {
            Log::error($ex->getMessage());
            $this->errorResponse($ex->getMessage());
        }
    }


    /**
     * @OA\Put(
     *     path="/users/profile",
     *     summary="Edit user profile",
     *     tags={"Profile"},
     *     security={{"jwt":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="firstname", type="string"),
     *             @OA\Property(property="lastname", type="string"),
     *             @OA\Property(property="image", type="string"),
     *             @OA\Property(property="mobile", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Profile updated successfully"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Profile update failed"
     *     )
     * )
     */
    public function update(ChangeProfileRequest $request)
    {
        try {
            $user = Auth::user();

            $user = $this->users->update($user->id, $request->all());

            return $this->successResponse($user);
        } catch (Exception $ex) {
            Log::error($ex->getMessage());
            $this->errorResponse($ex->getMessage());
        }
    }

    /**
     * @OA\Post(
     *     path="/users/profile/image",
     *     summary="Upload Profile Image",
     *     tags={"Profile"},
     *     security={{"jwt":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"image"},
     *                 @OA\Property(
     *                     property="image",
     *                     type="string",
     *                     format="binary",
     *                     description="Profile image file"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Profile image uploaded successfully"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Profile image upload failed"
     *     )
     * )
     */
    public function image(Request $request)
    {
        try {
            $request->validate([
                'image' => 'required|file|mimes:jpeg,png,webp|max:30240', // max بر حسب KB
            ], [
                'image.max' => 'File size must not exceed 30MB.',
            ]);

            if (!$request->hasFile('image')) {
                return $this->errorResponse('No image uploaded', 422);
            }

            $user = Auth::user();
            $image = $this->users->uploadProfileImage($user->id, $request->file('image'));

            $data_user = $this->users->find($user->id);
            $data_image = [
                'image' => $image,
            ];
            $this->users->update($data_user->id, $data_image);

            return $this->successResponse([
                'message' => 'Profile image uploaded successfully',
                'image' => $image,
                'image_url' => asset('images/'.$user->id.'/profile/'. $image)
            ]);
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            return $this->errorResponse($ex->getMessage(), 500);
        }
    }



    /**
     * @OA\Get(
     *     path="/users/profile/email",
     *     summary="show list email sended for user",
     *     tags={"Profile"},
     *     security={{"jwt":{}}},
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Items per page",
     *         required=false,
     *         @OA\Schema(type="integer", default=15)
     *     ),
     *     @OA\Parameter(
     *         name="type",
     *         in="query",
     *         description="filter type of email for example => 0=signup and active email,1=forgetpassword",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Email list successfully"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Failed to get list"
     *     )
     * )
     */
    public function email(Request $request)
    {
        try {
            $user = Auth::user();
            $type = $request->query('type');

            $list = $this->emails->findByUserId($user->id, $type);

            return $this->successResponse($list);
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            return $this->errorResponse($ex->getMessage());
        }
    }

    /**
     * @OA\Post(
     *     path="/users/profile/logout",
     *     summary="logout user",
     *     tags={"Profile"},
     *     security={{"jwt":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="User logout successfully"
     *     ),
     * @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="logout failed"
     *     )
     * )
     */
    public function logout()
    {
        try {
            Auth::user()->logout;

            return response()->json([
                'message' => 'User logout successfully'
            ], 200);
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            $this->errorResponse($ex->getMessage());
        }
    }
}
