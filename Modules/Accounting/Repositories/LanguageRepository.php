<?php

namespace Modules\Accounting\Repositories;

use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Model;
use Modules\Accounting\Entities\Languages;

class LanguageRepository extends BaseRepository
{
    protected Model $model;

    public function __construct(Languages $model)
    {
        $this->model = $model;
    }
}