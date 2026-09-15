<?php

namespace Modules\System\Repositories;

use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Model;
use Modules\System\Entities\Language;

class LanguageRepository extends BaseRepository
{
    protected Model $model;

    public function __construct(Language $model)
    {
        $this->model = $model;
    }
}
