<?php

namespace Modules\Accounting\Services;


use App\Services\BaseService;
use Modules\Accounting\Repositories\LanguageRepository;

class LanguageService extends BaseService
{
    public function __construct(LanguageRepository $language)
    {
        parent::__construct($language);
        $this->language = $language;
    }
}
