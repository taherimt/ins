<?php

namespace App\Services;

use App\Repositories\Contracts\BaseRepositoryInterface;

class DataService
{
    protected $repository;

    public function __construct(BaseRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function store(...$params)
    {
        return $this->repository->store(...$params);
    }
}
