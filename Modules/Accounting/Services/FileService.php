<?php

namespace Modules\Accounting\Services;


use App\Services\BaseService;
use Modules\Accounting\Repositories\FileRepository;

class FileService extends BaseService
{
    public function __construct(FileRepository $file)
    {
        parent::__construct($file);
        $this->file = $file;
    }
}
