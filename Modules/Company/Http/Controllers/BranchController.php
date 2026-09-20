<?php

namespace Modules\Company\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use Modules\Company\Http\Requests\Branch\CreateBranchRequest;
use Modules\Company\Http\Requests\Branch\UpdateBranchRequest;
use Modules\Company\Services\BranchService;

class BranchController extends Controller
{
    public function __construct(protected BranchService $branchService) {}

    public function index() { $branches = $this->branchService->getAll([], ['*'], ['company', 'country'], ['id' => 'desc']); return view('company::branch.index', compact('branches')); }
    public function create() { return view('company::branch.create'); }
    public function store(CreateBranchRequest $request): RedirectResponse { $result = $this->branchService->create($request->validated()); return $this->redirectResult($result); }
    public function show(int $branch) { $branch = $this->branchService->findOrFail($branch, ['company', 'country']); return view('company::branch.show', compact('branch')); }
    public function edit(int $branch) { $branch = $this->branchService->findOrFail($branch); return view('company::branch.edit', compact('branch')); }
    public function update(UpdateBranchRequest $request, int $branch): RedirectResponse { $result = $this->branchService->update($branch, $request->validated()); return $this->redirectResult($result); }
    public function destroy(int $branch): RedirectResponse { $result = $this->branchService->delete($branch); return $this->redirectResult($result); }
    private function redirectResult(array $result): RedirectResponse { if (!($result['success'] ?? false)) return back()->withInput()->withErrors(['message' => $result['message'] ?? 'Operation failed.']); return redirect()->route('company.branch.index')->with('success', $result['message'] ?? 'Operation completed successfully.'); }
}
