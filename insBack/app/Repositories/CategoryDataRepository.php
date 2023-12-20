<?php

namespace App\Repositories;

use App\Models\Category;
use App\Repositories\Contracts\BaseRepositoryInterface;
use App\Services\Response;

class CategoryDataRepository implements BaseRepositoryInterface
{
    public function store(...$params)
    {
        $data = $params[0];
        $category = Category::create($data);

        if (!$category->exists) {
            return Response::error( 'category was not created');
        }

        return $category;
    }
}
