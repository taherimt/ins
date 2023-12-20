<?php

namespace App\Repositories;

use App\Jobs\Guardian\FetchGuardianNewsWithQuery;
use App\Jobs\NewsApi\FetchNewsApiWithQuery;
use App\Models\Article;
use Illuminate\Contracts\Pagination\Paginator;

class ArticleRepository
{
    public function getArticles($specifications): Paginator
    {
        $query = Article::query()->with(['source', 'category']);
        foreach ($specifications as $specification) {
            $specification->apply($query);
        }
        return $query->simplePaginate(10);
    }

    public function searchArticle($search)
    {
//        FetchGuardianNewsWithQuery::dispatch(1,$search);
        FetchNewsApiWithQuery::dispatch($search,1);

        if ($search) {
            $articles = Article::where('title', 'LIKE', "%{$search}%")
                ->orWhere('content', 'LIKE', "%{$search}%")
                ->orWhere('author', 'LIKE', "%{$search}%")
                ->simplePaginate(10);
        } else {
            $articles = Article::simplePaginate(10);
        }

        return $articles;


    }
}
