<?php

namespace Modules\Setting\Http\Controllers\v1;

use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Modules\Setting\Http\Requests\Email\EmailRequest;
use Modules\Setting\Http\Requests\CommentRequest;
use Modules\Setting\Services\CommentService;
use Modules\Setting\Services\EmailService;

class CommentController extends BaseController
{
    protected $commentService;
    protected $emailService;

    public function __construct(CommentService $commentService, EmailService $emailService)
    {
        $this->commentService = $commentService;
        $this->emailService = $emailService;
    }

    /**
     * @OA\Get(
     *     path="/comments",
     *     summary="show list of commentss",
     *     tags={"Comments"},
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
     *     @OA\Response(
     *         response=200,
     *         description="State list retrieved successfully"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Failed to get state list"
     *     )
     * )
     */
    public function index()
    {
        try {
            $perPage = request()->get('per_page', 15);
            $comments = $this->commentService->getPaginatedComments($perPage);
            return $this->successResponse($comments);
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            return $this->errorResponse($ex->getMessage());
        }
    }


    /**
     * @OA\Post(
     *     path="/comments",
     *     summary="Create a new comment",
     *     tags={"Comments"},
     *     security={{"jwt":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"description","score"},
     *             @OA\Property(property="title", type="string", example="comment about website"),
     *             @OA\Property(property="description", type="string", example="tyhis is a very good website"),
     *             @OA\Property(property="score", type="integer", example=5)
     *         )
     *     ),
     *     @OA\Response(response=201, description="comment created successfully"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function store(CommentRequest $request)
    {
        try {
            $comment = $this->commentService->createComment(Auth::id(), $request->all());
            return $this->successResponse($comment, 'Comment created successfully');
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }


    /**
     * @OA\Post(
     *     path="/emails",
     *     summary="Create a contact",
     *     tags={"Email"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="firstname", type="string", example="masood"),
     *             @OA\Property(property="lastname", type="string", example="moazeni"),
     *             @OA\Property(property="email", type="string", example="aaaaaaaa@gmail.com")
     *         )
     *     ),
     *     @OA\Response(response=201, description="comment created successfully"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function emails(EmailRequest $request)
    {
        try {

            $email = $this->emailService->create($request->all());
            
            return $this->successResponse($email, 'email created successfully');
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }
}
