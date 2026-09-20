<?php

namespace Modules\System\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use Modules\System\Http\Requests\Currency\CreateCurrencyRequest;
use Modules\System\Http\Requests\Currency\UpdateCurrencyRequest;
use Modules\System\Services\CurrencyService;

class CurrencyController extends Controller
{
    public function __construct(protected CurrencyService $currencyService)
    {
    }

    public function index()
    {
        $currencies = $this->currencyService->getAll();

        return view('system::currency.index', compact('currencies'));
    }

    public function create()
    {
        return view('system::currency.create');
    }

    public function store(CreateCurrencyRequest $request): RedirectResponse
    {
        $result = $this->currencyService->create($request->validated());

        if (!($result['success'] ?? false)) {
            return back()->withInput()->withErrors(['message' => $result['message'] ?? 'Unable to create currency.']);
        }

        return redirect()->route('system.currency.index')->with('success', $result['message'] ?? 'Currency created successfully.');
    }

    public function show(int $id)
    {
        $currency = $this->currencyService->findOrFail($id);

        return view('system::currency.show', compact('currency'));
    }

    public function edit(int $id)
    {
        $currency = $this->currencyService->findOrFail($id);

        return view('system::currency.edit', compact('currency'));
    }

    public function update(UpdateCurrencyRequest $request, int $id): RedirectResponse
    {
        $result = $this->currencyService->update($id, $request->validated());

        if (!($result['success'] ?? false)) {
            return back()->withInput()->withErrors(['message' => $result['message'] ?? 'Unable to update currency.']);
        }

        return redirect()->route('system.currency.index')->with('success', $result['message'] ?? 'Currency updated successfully.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $result = $this->currencyService->delete($id);

        if (!($result['success'] ?? false)) {
            return back()->withErrors(['message' => $result['message'] ?? 'Unable to delete currency.']);
        }

        return redirect()->route('system.currency.index')->with('success', $result['message'] ?? 'Currency deleted successfully.');
    }
}
