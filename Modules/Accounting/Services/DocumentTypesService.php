<?php

namespace Modules\Accounting\Services;


use App\Services\BaseService;
use Modules\Accounting\Repositories\DocumentTypeRepository;

class DocumentTypeService extends BaseService
{
    public function __construct(DocumentTypeRepository $documentType)
    {
        parent::__construct($documentType);
        $this->documentType = $documentType;
    }
}
