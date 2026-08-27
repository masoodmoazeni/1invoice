<?php

namespace Modules\Accounting\Repositories;

use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Model;
use Modules\Accounting\Entities\File;

class FileRepository extends BaseRepository
{
    protected Model $model;

    public function __construct(File $model)
    {
        $this->model = $model;
    }
}
