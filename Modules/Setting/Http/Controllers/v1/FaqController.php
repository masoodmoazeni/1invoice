<?php

namespace Modules\Setting\Http\Controllers\v1;

use App\Http\Controllers\BaseController;
use Exception;
use Illuminate\Support\Facades\Log;
use Modules\Setting\Services\FaqService;
use Modules\Setting\Transformers\FaqPublicResource;

class FaqController extends BaseController
{
    public function __construct(protected FaqService $service)
    {
    }

    public function index()
    {
        try {
            $categories = $this->service->getPublishedGrouped();
            $settings = $this->service->getSettings();

            return response()->json([
                'success' => true,
                'message' => 'The operation was successful.',
                'data' => FaqPublicResource::collection($categories)->resolve(),
                'schema' => $settings->schema,
                'meta_title' => $settings->meta_title,
                'meta_description' => $settings->meta_description,
            ]);
        } catch (Exception $ex) {
            Log::error('FAQ INDEX ERROR', ['error' => $ex->getMessage()]);

            return $this->errorResponse('Failed to fetch FAQ');
        }
    }
}
