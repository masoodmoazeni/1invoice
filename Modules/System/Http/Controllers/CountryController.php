<?php

namespace Modules\System\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\System\Http\Requests\Country\CreateCountryRequest;
use Modules\System\Http\Requests\Country\UpdateCountryRequest;
use Modules\System\Services\CountryService;

class CountryController extends Controller
{
    public function __construct(protected CountryService $countryService)
    {
    }

    public function index()
    {
        $countries = $this->countryService->getAll();

        return view('system::country.index', compact('countries'));
    }

    public function create()
    {
        return view('system::country.create');
    }

    public function store(CreateCountryRequest $request): RedirectResponse
    {
        $result = $this->countryService->create($request->validated());

        if (!($result['success'] ?? false)) {
            return back()->withInput()->withErrors(['message' => $result['message'] ?? 'Unable to create country.']);
        }

        return redirect()->route('system.country.index')->with('success', $result['message'] ?? 'Country created successfully.');
    }

    public function show(int $id)
    {
        $country = $this->countryService->findOrFail($id);

        return view('system::country.show', compact('country'));
    }

    public function edit(int $id)
    {
        $country = $this->countryService->findOrFail($id);

        return view('system::country.edit', compact('country'));
    }

    public function update(UpdateCountryRequest $request, int $id): RedirectResponse
    {
        $result = $this->countryService->update($id, $request->validated());

        if (!($result['success'] ?? false)) {
            return back()->withInput()->withErrors(['message' => $result['message'] ?? 'Unable to update country.']);
        }

        return redirect()->route('system.country.index')->with('success', $result['message'] ?? 'Country updated successfully.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $result = $this->countryService->delete($id);

        if (!($result['success'] ?? false)) {
            return back()->withErrors(['message' => $result['message'] ?? 'Unable to delete country.']);
        }

        return redirect()->route('system.country.index')->with('success', $result['message'] ?? 'Country deleted successfully.');
    }
}
