<?php

namespace Modules\System\Services;


use App\Services\BaseService;
use Modules\System\Repositories\LanguageRepository;

class LanguageService extends BaseService
{
    public function __construct(LanguageRepository $language)
    {
        parent::__construct($language);
        $this->language = $language;
    }
}
