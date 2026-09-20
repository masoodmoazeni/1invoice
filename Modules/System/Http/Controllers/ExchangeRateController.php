<?php

namespace Modules\System\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use Modules\System\Http\Requests\ExchangeRate\CreateExchangeRateRequest;
use Modules\System\Http\Requests\ExchangeRate\UpdateExchangeRateRequest;
use Modules\System\Services\ExchangeRateService;

class ExchangeRateController extends Controller
{
    public function __construct(protected ExchangeRateService $exchangeRateService)
    {
    }

    public function index()
    {
        $exchangeRates = $this->exchangeRateService->getAll();

        return view('system::exchange-rate.index', compact('exchangeRates'));
    }

    public function create()
    {
        return view('system::exchange-rate.create');
    }

    public function store(CreateExchangeRateRequest $request): RedirectResponse
    {
        $result = $this->exchangeRateService->create($request->validated());

        if (!($result['success'] ?? false)) {
            return back()->withInput()->withErrors(['message' => $result['message'] ?? 'Unable to create exchange rate.']);
        }

        return redirect()->route('system.exchange-rate.index')->with('success', $result['message'] ?? 'Exchange rate created successfully.');
    }

    public function show(int $id)
    {
        $exchangeRate = $this->exchangeRateService->findOrFail($id);

        return view('system::exchange-rate.show', compact('exchangeRate'));
    }

    public function edit(int $id)
    {
        $exchangeRate = $this->exchangeRateService->findOrFail($id);

        return view('system::exchange-rate.edit', compact('exchangeRate'));
    }

    public function update(UpdateExchangeRateRequest $request, int $id): RedirectResponse
    {
        $result = $this->exchangeRateService->update($id, $request->validated());

        if (!($result['success'] ?? false)) {
            return back()->withInput()->withErrors(['message' => $result['message'] ?? 'Unable to update exchange rate.']);
        }

        return redirect()->route('system.exchange-rate.index')->with('success', $result['message'] ?? 'Exchange rate updated successfully.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $result = $this->exchangeRateService->delete($id);

        if (!($result['success'] ?? false)) {
            return back()->withErrors(['message' => $result['message'] ?? 'Unable to delete exchange rate.']);
        }

        return redirect()->route('system.exchange-rate.index')->with('success', $result['message'] ?? 'Exchange rate deleted successfully.');
    }
}
