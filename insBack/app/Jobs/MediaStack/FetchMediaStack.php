<?php

namespace App\Jobs\MediaStack;

use App\Models\Article;
use App\Models\Category;
use App\Models\Source;
use Carbon\Carbon;
use GuzzleHttp\Exception\ConnectException;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FetchMediaStack implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected int $pageSize = 2;

    public function __construct(protected int $page = 1)
    {
        $this->onQueue('MediaStack');
    }


    /**
     * Execute the job.
     */
    public function handle()
    {
        $source = Source::query()->where('name', env('MEDIA_STACK', 'Media Stack'))->first();
        if (!$source) {
            return;
        }

        try {
            $response = Http::timeout(60)->get($source->api_endpoint, [
                'access_key' => $source->api_key,
                'limit' => $this->pageSize,
                'offset' => $this->page,
            ]);
        } catch (ConnectException $e) {
            Log::error("Request to The Media Stack API failed: " . $e->getMessage());
        }
        if ($response->failed()) {
            Log::error('Failed to fetch articles from The Media Stack API. Response: ' . $response->body());
            return;
        }


        $articles =  $response->json()['data'];

        foreach ($articles as $articleData) {
            $this->storeArticle($articleData, $source->id);
        }



    }

    protected function storeArticle($data, $sourceId)
    {
        $category = Category::firstOrCreate([
            'name' =>  $data['category'] ,
            'source_id' => $sourceId,
        ]);

        Article::updateOrCreate(
            ['url' => $data['url']],
            [
                'author' => $data['author'],
                'source_id' => $sourceId,
                'category_id' => $category->id,
                'content' => $data['description'],
                'image' => $data['image'],
                'title' => $data['title'],
                'published_at' => Carbon::parse($data['published_at'])->format('Y-m-d H:i:s'),
            ]
        );
    }
    /**
     * Handle a job failure.
     */
    public function failed(\Exception $exception)
    {
        Log::error("Job failed: " . $exception->getMessage());
    }
}
