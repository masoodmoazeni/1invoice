<?php

namespace Modules\Company\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Modules\Company\Entities\Branch;
use Modules\Company\Http\Requests\Branch\CreateBranchRequest;
use Modules\Company\Http\Requests\Branch\UpdateBranchRequest;
use Modules\Company\Resources\BranchResource;
use Modules\Company\Resources\CompanyCollection;

class BranchApiController extends Controller
{
    public function index() { return BranchResource::collection(Branch::with(['company', 'country'])->latest('id')->paginate()); }
    public function store(CreateBranchRequest $request) { return new BranchResource(Branch::create($request->validated())); }
    public function show(Branch $branch) { return new BranchResource($branch->load(['company', 'country'])); }
    public function update(UpdateBranchRequest $request, Branch $branch) { $branch->update($request->validated()); return new BranchResource($branch->fresh()); }
    public function destroy(Branch $branch): JsonResponse { $branch->delete(); return response()->json(null, 204); }
}
