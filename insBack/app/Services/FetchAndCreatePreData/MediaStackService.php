<?php

namespace App\Services\FetchAndCreatePreData;

use App\Jobs\MediaStack\FetchMediaStackWithQuery;
use App\Models\Article;
use App\Models\Source;
use App\Repositories\ArticleDataRepository;
use App\Services\DataService;
use App\Services\FetchAndCreatePreData\NewsServiceInterface\NewsServiceInterface;
use App\Services\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MediaStackService implements NewsServiceInterface
{
    private static int $pageSize = 1;

    public static function fetchArticles($search)
    {
        try {
            FetchMediaStackWithQuery::dispatch($search,1);

            $source = Source::query()->where('name', env('MEDIA_STACK', 'Media Stack'))->first();
            if (!$source) {
                return;
            }
            $response = Http::timeout(60)->get($source->api_endpoint, [
                'keywords' => $search,
                'limit' => self::$pageSize,
                'access_key' => $source->api_key,
            ]);

            if ($response->failed()) {
                Log::error('Failed to fetch articles from Media Stack. Response: ' . $response->body());
                return;
            }

            $articles = $response->json()['articles'];

            foreach ($articles as $data) {
                $data = Article::mediaStackData($data);
                $articleService = new DataService(new ArticleDataRepository());
                $articleService->store($data, $source->id);
            }
        }catch (\Exception){

            Response::error('fetch data failed');
        }
    }
}
