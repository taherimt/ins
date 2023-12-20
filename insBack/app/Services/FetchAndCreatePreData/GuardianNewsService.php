<?php

namespace App\Services\FetchAndCreatePreData;

use App\Jobs\Guardian\FetchGuardianNewsWithQuery;
use App\Models\Article;
use App\Models\Source;
use App\Repositories\ArticleDataRepository;
use App\Services\DataService;
use App\Services\FetchAndCreatePreData\NewsServiceInterface\NewsServiceInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GuardianNewsService implements NewsServiceInterface
{
    private static int $pageSize = 1;

    public static function fetchArticles($search)
    {
        FetchGuardianNewsWithQuery::dispatch(1, $search);

        $source = Source::query()->where('name', env('GUARDIAN_SOURCE_NAME', 'The Guardian'))->first();
        if (!$source) {
            return;
        }
        $response = Http::timeout(60)->get($source->api_endpoint, [
            'q' => $search,
            'page-size' => self::$pageSize,
            'api-key' => $source->api_key,
        ]);


        if ($response->failed()) {
            Log::error('Failed to fetch articles from The Guardian API. Response: ' . $response->body());
            return;
        }
        $response = $response->json()['response'];

        $articles = $response['results'];

        foreach ($articles as $data) {
            $data = Article::guardianNewsData($data);
            $articleService = new DataService(new ArticleDataRepository());
            $articleService->store($data, $source->id);
        }
    }
}
