<?php

namespace Modules\Accounting\Services;


use App\Services\BaseService;
use Modules\Accounting\Repositories\DocumentTypeRepository;

class DocumentTypService extends BaseService
{
    public function __construct(DocumentTypeRepository $documentType)
    {
        parent::__construct($documentType);
        $this->documentType = $documentType;
    }
}
