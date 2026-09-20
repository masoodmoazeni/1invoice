<?php

namespace Modules\System\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use Modules\System\Http\Requests\Language\CreateLanguageRequest;
use Modules\System\Http\Requests\Language\UpdateLanguageRequest;
use Modules\System\Services\LanguageService;

class LanguageController extends Controller
{
    public function __construct(protected LanguageService $languageService)
    {
    }

    public function index()
    {
        $languages = $this->languageService->getAll();

        return view('system::language.index', compact('languages'));
    }

    public function create()
    {
        return view('system::language.create');
    }

    public function store(CreateLanguageRequest $request): RedirectResponse
    {
        $result = $this->languageService->create($request->validated());

        if (!($result['success'] ?? false)) {
            return back()->withInput()->withErrors(['message' => $result['message'] ?? 'Unable to create language.']);
        }

        return redirect()->route('system.language.index')->with('success', $result['message'] ?? 'Language created successfully.');
    }

    public function show(int $id)
    {
        $language = $this->languageService->findOrFail($id);

        return view('system::language.show', compact('language'));
    }

    public function edit(int $id)
    {
        $language = $this->languageService->findOrFail($id);

        return view('system::language.edit', compact('language'));
    }

    public function update(UpdateLanguageRequest $request, int $id): RedirectResponse
    {
        $result = $this->languageService->update($id, $request->validated());

        if (!($result['success'] ?? false)) {
            return back()->withInput()->withErrors(['message' => $result['message'] ?? 'Unable to update language.']);
        }

        return redirect()->route('system.language.index')->with('success', $result['message'] ?? 'Language updated successfully.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $result = $this->languageService->delete($id);

        if (!($result['success'] ?? false)) {
            return back()->withErrors(['message' => $result['message'] ?? 'Unable to delete language.']);
        }

        return redirect()->route('system.language.index')->with('success', $result['message'] ?? 'Language deleted successfully.');
    }
}
