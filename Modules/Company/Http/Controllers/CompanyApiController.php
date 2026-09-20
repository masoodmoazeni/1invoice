<?php

namespace Modules\Company\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Modules\Company\Entities\Company;
use Modules\Company\Http\Requests\Company\CreateCompanyRequest;
use Modules\Company\Http\Requests\Company\UpdateCompanyRequest;
use Modules\Company\Resources\CompanyCollection;
use Modules\Company\Resources\CompanyResource;

class CompanyApiController extends Controller
{
    public function index(): CompanyCollection
    {
        return new CompanyCollection(Company::query()->latest('id')->paginate());
    }

    public function store(CreateCompanyRequest $request): CompanyResource
    {
        return new CompanyResource(Company::create($request->validated()));
    }

    public function show(Company $company): CompanyResource
    {
        return new CompanyResource($company->load(['country', 'baseCurrency', 'language', 'timezone']));
    }

    public function update(UpdateCompanyRequest $request, Company $company): CompanyResource
    {
        $company->update($request->validated());

        return new CompanyResource($company->fresh());
    }

    public function destroy(Company $company): JsonResponse
    {
        $company->delete();

        return response()->json(null, 204);
    }
}
