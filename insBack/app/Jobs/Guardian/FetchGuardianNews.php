<?php

namespace App\Jobs\Guardian;

use App\Models\Article;
use App\Models\Source;
use App\Repositories\ArticleDataRepository;
use App\Services\DataService;
use GuzzleHttp\Exception\ConnectException;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FetchGuardianNews implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected int $pageSize = 2;

    public function __construct(protected int $page = 1)
    {
        $this->onQueue('GuardianNews');
    }

    /**
     * Execute the job.
     * @throws \Exception
     */
    public function handle()
    {
        $source = Source::query()->where('name', env('GUARDIAN_SOURCE_NAME', 'The Guardian'))->first();
        if (!$source) {
            return;
        }

        try {
            $response = Http::timeout(60)->get($source->api_endpoint, [
                'api-key' => $source->api_key,
                'page-size' => $this->pageSize,
                'page' => $this->page,
            ]);
        } catch (ConnectException $e) {
            Log::error("Request to The Guardian API failed: " . $e->getMessage());
        }
        if ($response->failed()) {
            Log::error('Failed to fetch articles from The Guardian API. Response: ' . $response->body());
            return;
        }

        $response = $response->json()['response'];

        $articles = $response['results'];
        $totalPages = $response['pages'];


        foreach ($articles as $data) {
            $data = Article::guardianNewsData($data);
            $articleService = new DataService(new ArticleDataRepository());
            $articleService->store($data, $source->id);
        }

        if ($this->page < $totalPages) {
            dispatch(new FetchGuardianNews($this->page + 1));
        }

    }

    /**
     * Handle a job failure.
     */
    public function failed(\Exception $exception)
    {
        Log::error("Job failed: " . $exception->getMessage());
    }
}
