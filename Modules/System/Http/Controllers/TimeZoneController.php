<?php

namespace Modules\System\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use Modules\System\Http\Requests\TimeZone\CreateTimeZoneRequest;
use Modules\System\Http\Requests\TimeZone\UpdateTimeZoneRequest;
use Modules\System\Services\TimeZoneService;

class TimeZoneController extends Controller
{
    public function __construct(protected TimeZoneService $timeZoneService)
    {
    }

    public function index()
    {
        $timeZones = $this->timeZoneService->getAll();

        return view('system::time-zone.index', compact('timeZones'));
    }

    public function create()
    {
        return view('system::time-zone.create');
    }

    public function store(CreateTimeZoneRequest $request): RedirectResponse
    {
        $result = $this->timeZoneService->create($request->validated());

        if (!($result['success'] ?? false)) {
            return back()->withInput()->withErrors(['message' => $result['message'] ?? 'Unable to create timezone.']);
        }

        return redirect()->route('system.time-zone.index')->with('success', $result['message'] ?? 'Timezone created successfully.');
    }

    public function show(int $id)
    {
        $timeZone = $this->timeZoneService->findOrFail($id);

        return view('system::time-zone.show', compact('timeZone'));
    }

    public function edit(int $id)
    {
        $timeZone = $this->timeZoneService->findOrFail($id);

        return view('system::time-zone.edit', compact('timeZone'));
    }

    public function update(UpdateTimeZoneRequest $request, int $id): RedirectResponse
    {
        $result = $this->timeZoneService->update($id, $request->validated());

        if (!($result['success'] ?? false)) {
            return back()->withInput()->withErrors(['message' => $result['message'] ?? 'Unable to update timezone.']);
        }

        return redirect()->route('system.time-zone.index')->with('success', $result['message'] ?? 'Timezone updated successfully.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $result = $this->timeZoneService->delete($id);

        if (!($result['success'] ?? false)) {
            return back()->withErrors(['message' => $result['message'] ?? 'Unable to delete timezone.']);
        }

        return redirect()->route('system.time-zone.index')->with('success', $result['message'] ?? 'Timezone deleted successfully.');
    }
}
