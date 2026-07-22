<?php

namespace Modules\Setting\Services;

use Modules\Setting\Repositories\EmailRepository;

class EmailService
{
    protected $repository;

    public function __construct(EmailRepository $repository)
    {
        $this->repository = $repository;
    }

    public function create(array $data)
    {
        return $this->repository->create($data);
    }
}
