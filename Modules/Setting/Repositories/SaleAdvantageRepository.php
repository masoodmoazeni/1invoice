<?php

namespace Modules\Setting\Repositories;

use Illuminate\Support\Facades\DB;
use Modules\Setting\Entities\SaleAdvantage;

class SaleAdvantageRepository
{
    protected $model;

    public function __construct(SaleAdvantage $model)
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

        $record->type          = $data['type'] ?? $record->type;
        $record->title         = $data['title'] ?? $record->title;
        $record->description   = $data['description'] ?? $record->description;
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

    public function paginateWithSearch($perPage = 15, $search = null, $type = null)
    {
        $query = $this->model->query();

        if (!empty($search)) {
            $query->where('title', 'LIKE', "%{$search}%");
        }

        if (!is_null($type)) {
            $query->where('type', $type);
        } else {
            $query->whereNull('type');
        }

        return $query->paginate($perPage);
    }

    public function getByType($type)
    {
        return $this->model
            ->where('type', $type)
            ->where('status', 1)
            ->orderBy('id', 'asc')
            ->get(['id', 'title', 'description', 'type']);
    }

    public function getAllTypesTreeWithCountList($type, $ids = [])
    {
        
        if (empty($ids)) {
            $items = $this->model
                ->where('type', $type)
                ->where('status', 1)
                ->orderBy('id', 'asc')
                ->get(['id', 'title', 'description', 'type']);
            foreach ($items as $item) {
                $item->count = 0;
            }
            return $items->values();
        }
        $countsQuery = DB::table('list_type_ofs')
            ->select('business_type_id', DB::raw('COUNT(DISTINCT list_id) as cnt'))
            ->whereNull('deleted_at')
            ->where('type', $type)
            ->whereIn('list_id', $ids)
            ->groupBy('business_type_id')
            ->pluck('cnt', 'business_type_id');
        $items = $this->model
            ->where('type', $type)
            ->where('status', 1)
            ->orderBy('id', 'asc')
            ->get(['id', 'title', 'description', 'type']);
        foreach ($items as $item) {
            $item->count = isset($countsQuery[$item->id]) ? (int) $countsQuery[$item->id] : 0;
        }
        return $items->values();
    }

}
