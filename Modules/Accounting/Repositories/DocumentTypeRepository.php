<?php

namespace Modules\Accounting\Repositories;

use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Model;
use Modules\Accounting\Entities\DocumentType;

class DocumentTypeRepository extends BaseRepository
{
    protected Model $model;

    public function __construct(DocumentType $model)
    {
        $this->model = $model;
    }
}
