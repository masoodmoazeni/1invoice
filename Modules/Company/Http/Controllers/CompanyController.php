<?php

namespace Modules\Company\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use Modules\Company\Http\Requests\Company\CreateCompanyRequest;
use Modules\Company\Http\Requests\Company\UpdateCompanyRequest;
use Modules\Company\Services\CompanyService;

class CompanyController extends Controller
{
    public function __construct(protected CompanyService $companyService)
    {
    }

    public function index()
    {
        $companies = $this->companyService->getAll([], ['*'], [], ['id' => 'desc']);

        return view('company::company.index', compact('companies'));
    }

    public function create()
    {
        return view('company::company.create');
    }

    public function store(CreateCompanyRequest $request): RedirectResponse
    {
        $result = $this->companyService->create($request->validated());

        if (!($result['success'] ?? false)) {
            return back()->withInput()->withErrors(['message' => $result['message'] ?? 'Unable to create company.']);
        }

        return redirect()->route('company.company.index')->with('success', $result['message'] ?? 'Company created successfully.');
    }

    public function show(int $company)
    {
        $company = $this->companyService->findOrFail($company, ['country', 'baseCurrency', 'language', 'timezone']);

        return view('company::company.show', compact('company'));
    }

    public function edit(int $company)
    {
        $company = $this->companyService->findOrFail($company);

        return view('company::company.edit', compact('company'));
    }

    public function update(UpdateCompanyRequest $request, int $company): RedirectResponse
    {
        $result = $this->companyService->update($company, $request->validated());

        if (!($result['success'] ?? false)) {
            return back()->withInput()->withErrors(['message' => $result['message'] ?? 'Unable to update company.']);
        }

        return redirect()->route('company.company.index')->with('success', $result['message'] ?? 'Company updated successfully.');
    }

    public function destroy(int $company): RedirectResponse
    {
        $result = $this->companyService->delete($company);

        if (!($result['success'] ?? false)) {
            return back()->withErrors(['message' => $result['message'] ?? 'Unable to delete company.']);
        }

        return redirect()->route('company.company.index')->with('success', $result['message'] ?? 'Company deleted successfully.');
    }
}
