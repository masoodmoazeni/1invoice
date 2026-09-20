<?php

namespace Modules\Company\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Modules\Company\Entities\Department;
use Modules\Company\Http\Requests\Department\CreateDepartmentRequest;
use Modules\Company\Http\Requests\Department\UpdateDepartmentRequest;
use Modules\Company\Resources\DepartmentResource;

class DepartmentApiController extends Controller
{
    public function index() { return DepartmentResource::collection(Department::with(['company', 'parent'])->latest('id')->paginate()); }
    public function store(CreateDepartmentRequest $request) { return new DepartmentResource(Department::create($request->validated())); }
    public function show(Department $department) { return new DepartmentResource($department->load(['company', 'parent', 'children'])); }
    public function update(UpdateDepartmentRequest $request, Department $department) { $department->update($request->validated()); return new DepartmentResource($department->fresh()); }
    public function destroy(Department $department): JsonResponse { $department->delete(); return response()->json(null, 204); }
}
