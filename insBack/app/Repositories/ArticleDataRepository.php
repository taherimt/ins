<?php

namespace App\Repositories;

use App\Services\Response;
use App\Models\Article;
use App\Models\Category;
use App\Repositories\Contracts\BaseRepositoryInterface;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ArticleDataRepository implements BaseRepositoryInterface
{
    public function store(...$params)
    {
        $data = $params[0];
        $sourceId = $params[1] ?? null;
        try {
            DB::beginTransaction();
            $category = Category::firstOrCreate([
                'name' => $data['categoryName'],
                'source_id' => $sourceId,
            ]);

            $article = Article::updateOrCreate(
                ['url' => $data['url']],
                [
                    'title' => $data['title'] ?? null,
                    'author' => $data['author'] ?? null,
                    'source_id' => $sourceId,
                    'category_id' => $category->id,
                    'published_at' => $data['published_at'] ?? null,
                    'content' => $data['content'] ?? null,
                    'image' => $data['image'] ?? null,
                    'url' => $data['url'] ?? null,
                ]
            );


        } catch (\Exception $exception) {
            Log::critical('Article Error Is : ' . $exception->getMessage());
            DB::rollBack();
            return Response::error('article was not created');
        }

        if (!$article->exists) {
            return Response::error('article was not created');
        }
        DB::commit();
        return $article;
    }

    public function allArticles($specifications): Paginator
    {
        $query = Article::query()->with(['source','category']);
        foreach ($specifications as $specification) {
            $specification->apply($query);
        }
        return $query->simplePaginate(10);
    }
}
