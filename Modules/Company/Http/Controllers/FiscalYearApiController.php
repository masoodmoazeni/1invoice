<?php

namespace Modules\Company\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Modules\Company\Entities\FiscalYear;
use Modules\Company\Http\Requests\FiscalYear\CreateFiscalYearRequest;
use Modules\Company\Http\Requests\FiscalYear\UpdateFiscalYearRequest;
use Modules\Company\Resources\FiscalYearResource;

class FiscalYearApiController extends Controller
{
    public function index() { return FiscalYearResource::collection(FiscalYear::with('company')->latest('start_date')->paginate()); }
    public function store(CreateFiscalYearRequest $request) { return new FiscalYearResource(FiscalYear::create($request->validated())); }
    public function show(FiscalYear $fiscalYear) { return new FiscalYearResource($fiscalYear->load('company')); }
    public function update(UpdateFiscalYearRequest $request, FiscalYear $fiscalYear) { $fiscalYear->update($request->validated()); return new FiscalYearResource($fiscalYear->fresh()); }
    public function destroy(FiscalYear $fiscalYear): JsonResponse { $fiscalYear->delete(); return response()->json(null, 204); }
}
