<?php

namespace Modules\Accounting\Http\Controllers;

use App\Http\Controllers\BaseController;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Modules\Accounting\Services\CountryService;

class CountryController extends BaseController
{
    protected CountryService $countryService;

    public function __construct(
        CountryService $countryService,
    )
    {
        $this->countryService = $countryService;
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(Request $request)
    {
        $breadcrumbs = [
            ['label' => __('messages.menu.main'), 'url' => route('country.index')],
            ['label' => __('messages.menu.setting.item'), 'url' => route('country.index')],
            ['label' => __('messages.item.label.title_index')]
        ];

        $page = $request->get('page', 1);
        $filter = $request->get('filter', []);
        $perPage = $request->get('per_page', 15);

        $country = $this->countryService->getPaginate($perPage, $filter);

        return view('accounting::country.index' , compact('country', 'breadcrumbs'));
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('accounting::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('accounting::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('accounting::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        //
    }
}
