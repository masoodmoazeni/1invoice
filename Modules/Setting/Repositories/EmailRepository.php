<?php

namespace Modules\Setting\Repositories;

use Modules\Setting\Entities\Email;

class EmailRepository
{
    
    public function create(array $data)
    {
        return Email::create($data);
    }

}
