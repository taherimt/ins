<?php

namespace App\Repositories;

use App\Models\Source;
use App\Repositories\Contracts\BaseRepositoryInterface;
use App\Services\Response;

class SourceDataRepository implements BaseRepositoryInterface
{
    public function store(...$params)
    {
        $data = $params[0];
        $source = Source::create($data);
        if (!$source->exists) {
            return Response::error('source was not created');
        }
        return $source;
    }
}
