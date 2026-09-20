<?php

namespace Modules\Company\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use Modules\Company\Http\Requests\Department\CreateDepartmentRequest;
use Modules\Company\Http\Requests\Department\UpdateDepartmentRequest;
use Modules\Company\Services\DepartmentService;

class DepartmentController extends Controller
{
    public function __construct(protected DepartmentService $departmentService) {}
    public function index() { $departments = $this->departmentService->getAll([], ['*'], ['company', 'parent'], ['id' => 'desc']); return view('company::department.index', compact('departments')); }
    public function create() { return view('company::department.create'); }
    public function store(CreateDepartmentRequest $request): RedirectResponse { return $this->redirectResult($this->departmentService->create($request->validated())); }
    public function show(int $department) { $department = $this->departmentService->findOrFail($department, ['company', 'parent', 'children']); return view('company::department.show', compact('department')); }
    public function edit(int $department) { $department = $this->departmentService->findOrFail($department); return view('company::department.edit', compact('department')); }
    public function update(UpdateDepartmentRequest $request, int $department): RedirectResponse { return $this->redirectResult($this->departmentService->update($department, $request->validated())); }
    public function destroy(int $department): RedirectResponse { return $this->redirectResult($this->departmentService->delete($department)); }
    private function redirectResult(array $result): RedirectResponse { if (!($result['success'] ?? false)) return back()->withInput()->withErrors(['message' => $result['message'] ?? 'Operation failed.']); return redirect()->route('company.department.index')->with('success', $result['message'] ?? 'Operation completed successfully.'); }
}
