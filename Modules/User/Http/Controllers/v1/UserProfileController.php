<?php

namespace Modules\User\Http\Controllers\v1;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Modules\Admin\Transformers\AdminUserProfileResource;
use Modules\User\Services\UserProfileService;

/**
 * @OA\Tag(
 *     name="User",
 *     description="Operations about users"
 * )
 */
class UserProfileController extends BaseController
{

    protected $service;

    public function __construct(UserProfileService $service)
    {
        $this->service = $service;
    }

    /**
     * @OA\Get(
     *     path="/users/team",
     *     summary="Get all team members with pagination",
     *     tags={"User Team"},
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number",
     *         required=false,
     *         @OA\Schema(type="integer", default=1)
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Items per page",
     *         required=false,
     *         @OA\Schema(type="integer", default=15)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Team members retrieved successfully"
     *     )
     * )
     */
    public function index(Request $request)
    {
        try {
            $perPage = (int) $request->get('per_page', 15);

            $team = $this->service->getTeamMembers($perPage, true, true);

            return $this->successResponse(
                AdminUserProfileResource::collection($team)
            );
        } catch (\Exception $ex) {
            return $this->errorResponse('Failed to retrieve team members', 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/users/team/slugs",
     *     summary="List broker/team profile slugs (no pagination)",
     *     tags={"User Team"},
     *     @OA\Response(response=200, description="Slugs retrieved successfully")
     * )
     */
    public function slugs()
    {
        try {
            return $this->successResponse($this->service->pluckSlugs(true));
        } catch (\Exception $ex) {
            return $this->errorResponse('Failed to retrieve team slugs', 500);
        }
    }


    /**
     * @OA\Get(
     *     path="/users/team/{slug}",
     *     summary="Get single team member profile",
     *     tags={"User Team"},
     *     @OA\Parameter(name="slug", in="path", required=true, @OA\Schema(type="string")),
     *     @OA\Response(response=200, description="Profile retrieved successfully"),
     *     @OA\Response(response=404, description="Profile not found")
     * )
     */
    public function show($slug)
    {
        try {
            $profile = $this->service->getProfileSlug($slug);

            if (!$profile) {
                return $this->errorResponse('Profile not found', 404);
            }

            return $this->successResponse($profile);
        } catch (\Exception $ex) {
            return $this->errorResponse('Failed to retrieve profile', 500);
        }
    }
}
