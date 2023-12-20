<?php

namespace App\Services\FetchAndCreatePreData;

use App\Jobs\NewsApi\FetchNewsApiWithQuery;
use App\Models\Article;
use App\Models\Source;
use App\Repositories\ArticleDataRepository;
use App\Services\DataService;
use App\Services\FetchAndCreatePreData\NewsServiceInterface\NewsServiceInterface;
use App\Services\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NewsApiService implements NewsServiceInterface
{
    private static int $pageSize = 1;

    public static function fetchArticles($search)
    {
        try {
            FetchNewsApiWithQuery::dispatch($search,1);

            $source = Source::query()->where('name', env('NEWSAPI_SOURCE_NAME', 'The NewsApi'))->first();
            if (!$source) {
                return;
            }
            $response = Http::timeout(60)->get($source->api_endpoint, [
                'q' => $search,
                'pageSize' => self::$pageSize,
                'apiKey' => $source->api_key,
            ]);

            if ($response->failed()) {
                Log::error('Failed to fetch articles from NewsAPI. Response: ' . $response->body());
                return;
            }

            $articles = $response->json()['articles'];

            foreach ($articles as $data) {
                $data = Article::newsApiData($data);
                $articleService = new DataService(new ArticleDataRepository());
                $articleService->store($data, $source->id);
            }
        }catch (\Exception){
            Response::error('fetch data failed');
        }
    }
}
