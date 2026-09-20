<?php

namespace Modules\Company\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use Modules\Company\Http\Requests\FiscalYear\CreateFiscalYearRequest;
use Modules\Company\Http\Requests\FiscalYear\UpdateFiscalYearRequest;
use Modules\Company\Services\FiscalYearService;

class FiscalYearController extends Controller
{
    public function __construct(protected FiscalYearService $fiscalYearService) {}
    public function index() { $fiscalYears = $this->fiscalYearService->getAll([], ['*'], ['company'], ['start_date' => 'desc']); return view('company::fiscal-year.index', compact('fiscalYears')); }
    public function create() { return view('company::fiscal-year.create'); }
    public function store(CreateFiscalYearRequest $request): RedirectResponse { return $this->redirectResult($this->fiscalYearService->create($request->validated())); }
    public function show(int $fiscalYear) { $fiscalYear = $this->fiscalYearService->findOrFail($fiscalYear, ['company']); return view('company::fiscal-year.show', compact('fiscalYear')); }
    public function edit(int $fiscalYear) { $fiscalYear = $this->fiscalYearService->findOrFail($fiscalYear); return view('company::fiscal-year.edit', compact('fiscalYear')); }
    public function update(UpdateFiscalYearRequest $request, int $fiscalYear): RedirectResponse { return $this->redirectResult($this->fiscalYearService->update($fiscalYear, $request->validated())); }
    public function destroy(int $fiscalYear): RedirectResponse { return $this->redirectResult($this->fiscalYearService->delete($fiscalYear)); }
    private function redirectResult(array $result): RedirectResponse { if (!($result['success'] ?? false)) return back()->withInput()->withErrors(['message' => $result['message'] ?? 'Operation failed.']); return redirect()->route('company.fiscal-year.index')->with('success', $result['message'] ?? 'Operation completed successfully.'); }
}
