<?php

namespace Modules\Setting\Repositories;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Setting\Entities\Country;

class CountryRepository
{
    protected $model;

    public function __construct(Country $model)
    {
        $this->model = $model;
    }

    public function all()
    {
        return $this->model->all();
    }

    public function find($id)
    {
        return $this->model->find($id);
    }

    public function findByName($name)
    {
        $query = $this->model->newQuery();

        $query->where('name', 'LIKE', "%{$name}%");

        return $query->first();
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update($id, array $data)
    {
        $record = $this->model->find($id);
        if (!$record) {
            return false;
        }

        $record->name          = $data['name'] ?? $record->name;
        $record->country_code  = $data['country_code'] ?? $record->country_code;
        $record->description   = $data['description'] ?? $record->description;
        $record->flag          = $data['flag'] ?? $record->flag;
        $record->status        = $data['status'] ?? $record->status;

        $record->save($data);

        return $record;
    }

    public function delete($id)
    {
        $record = $this->model->find($id);
        if (!$record) {
            return false;
        }

        return $record->delete();
    }

    public function paginateWithSearch($perPage = 15, $search = null)
    {
        $query = $this->model->newQuery();

        if (!empty($search)) {
            $query->where('name', 'LIKE', "%{$search}%");
        }

        return $query->orderby('id', 'desc')->paginate($perPage);
    }

    public function allWithHierarchy()
    {
        return $this->model->with(['states.cities'])->get();
    }

    public function allWithHierarchyWithCountList($ids = [], $countryIds = [], $stateIds = [], $cityIds = [])
    {
        $countries = $this->model
            ->with(['states'])->orderby('id', 'desc')
            ->get();

        $result = [];

        $shouldCount = !empty($ids) || !empty($countryIds) || !empty($stateIds) || !empty($cityIds);

        foreach ($countries as $country) {

            if ($shouldCount) {
                $countryCount = DB::table('lists')
                    ->leftJoin('list_states', 'lists.id', '=', 'list_states.list_id')
                    ->where('lists.country_id', $country->id)
                    ->when(!empty($ids), fn($q) => $q->whereIn('lists.id', $ids))
                    ->when(!empty($stateIds), fn($q) => $q->whereIn('list_states.state_id', $stateIds))
                    ->when(!empty($cityIds), fn($q) => $q->whereIn('list_states.city_id', $cityIds))
                    ->count();
            } else {
                $countryCount = 0;
            }

            $countryArray = [
                'id' => $country->id,
                'name' => $country->name,
                'country_code' => $country->country_code,
                'description' => $country->description,
                'flag' => $country->flag,
                'status' => $country->status,
                'count' => $countryCount,
                'states' => []
            ];

            foreach ($country->states as $state) {

                if ($shouldCount) {
                    $stateCount = DB::table('lists')
                        ->join('list_states', 'lists.id', '=', 'list_states.list_id')
                        ->when(!empty($ids), fn($q) => $q->whereIn('lists.id', $ids))
                        ->when(!empty($countryIds), fn($q) => $q->whereIn('lists.country_id', $countryIds))
                        ->where('list_states.state_id', $state->id)
                        ->when(!empty($cityIds), fn($q) => $q->whereIn('list_states.city_id', $cityIds))
                        ->count();
                } else {
                    $stateCount = 0;
                }

                $stateArray = [
                    'id' => $state->id,
                    'country_id' => $state->country_id,
                    'state_code' => $state->state_code,
                    'name' => $state->name,
                    'slug' => $this->makeSlug($state->name),
                    'description' => $state->description,
                    'disclaimer' => $state->disclaimer,
                    'status' => $state->status,
                    'count' => $stateCount,
                    'cities' => []
                ];
                $countryArray['states'][] = $stateArray;
            }

            $result[] = $countryArray;
        }

        return $result;
    }

    protected function makeSlug($string)
    {
        $slug = mb_strtolower($string); // تبدیل به کوچک
        $slug = preg_replace('/\s+/', '-', $slug); // فاصله‌ها → -
        $slug = preg_replace('/[^\p{L}\p{N}-]+/u', '', $slug); // حذف کاراکترهای اضافی
        return $slug;
    }
}
