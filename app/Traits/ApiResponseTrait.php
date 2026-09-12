<?php

namespace App\Traits;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\Auth;

trait ApiResponseTrait
{
    protected function checkPermission($permission, $model = null, $message = null)
    {
        try{
            $user = auth()->user();

            if (!$user) {
                if (request()->ajax() || request()->wantsJson()) {
                    return response()->json(['error' => __('messages.login.please_enter')], 403);
                }

                return response()->view('layout.pages.403', [
                    'message' => __('messages.login.please_enter'),
                    'previousUrl' => route('login'),
                ], 403);
            }

            $hasPermission = $user->hasPermission($permission);

            if ($model && method_exists($this, 'checkOwnership')) {
                $hasPermission = $hasPermission && $this->checkOwnership($user, $model);
            }

            if (!$hasPermission) {
                $message = $message ?? __('messages.global.label.you_do_not_have_permission_to_perform_this_operation');

                if (request()->ajax() || request()->wantsJson()) {
                    return response()->json(['error' => $message], 403);
                }

                response()->view('layout.pages.403', [
                    'message' => $message,
                    'previousUrl' => url()->previous()
                ], 403)->send();
                exit;
            }

        }catch(\Exception $e){
            \Log::error('Login error: ' . $e->getMessage());
            return $this->errorResponse($e->getMessage(), __('messages.global.messages.has_occurred'), 500);
        }
    }

    protected function canIndex($permission = null)
    {
        return $this->checkPermission($permission);
    }

    protected function canCreate($permission = null)
    {
        return $this->checkPermission($permission);
    }

    protected function canShow($permission = null, $model = null)
    {
        return $this->checkPermission($permission, $model);
    }

    protected function canEdit($permission = null, $model = null)
    {
        return $this->checkPermission($permission, $model);
    }

    protected function canDelete($permission = null, $model = null)
    {
        return $this->checkPermission($permission, $model);
    }

    protected function canMenu($permission = null, $model = null)
    {
        return $this->checkPermission($permission, $model);
    }

    protected function checkOwnership($user, $model)
    {
        return $user->id === $model->id || $user->hasPermission('admin_access');
    }

    // see all permission
    protected function getUserPermissionsForEntity($entityName)
    {
        $user = Auth::user();

        if (!$user) {
            abort(403);
        }

        $actions = ['index', 'create', 'show', 'edit', 'delete'];
        $permissions = [];

        foreach ($actions as $action) {
            $permissionName = "{$entityName}_{$action}";
            $permissions[$action] = [
                'name' => $permissionName,
                'has' => $user->hasPermission($permissionName)
            ];
        }

        $hasAnyPermission = collect($permissions)->pluck('has')->contains(true);

        if (!$hasAnyPermission) {
            return redirect()->route('login')->with('error', $message);
            exit;
        }

        return [
            'user' => $user->username,
            'user_id' => $user->id,
            'entity' => $entityName,
            'permissions' => $permissions
        ];
    }
}
