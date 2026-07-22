<?php

namespace Modules\Setting\Http\Controllers\v1;

use App\Http\Controllers\BaseController;
use Exception;
use Illuminate\Support\Facades\Log;
use Modules\Setting\Services\HeaderMenuService;
use Modules\Setting\Transformers\HeaderMenuPublicResource;

class HeaderMenuController extends BaseController
{
    public function __construct(protected HeaderMenuService $service)
    {
    }

    public function index()
    {
        try {
            $items = $this->service->getPublishedMenu();

            return $this->successResponse(
                HeaderMenuPublicResource::collection($items)->resolve()
            );
        } catch (Exception $ex) {
            Log::error('HEADER MENU INDEX ERROR', ['error' => $ex->getMessage()]);

            return $this->errorResponse('Failed to fetch header menu');
        }
    }
}
