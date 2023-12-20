<?php

namespace App\Console\Commands;

use App\Jobs\Guardian\FetchGuardianNews;
use App\Jobs\MediaStack\FetchMediaStack;
use Illuminate\Console\Command;

class FetchNews extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:fetch-news';

    /**
     * The console command description.
     *
     * @var string
     */

    /**
     * Execute the console command.
     */

    protected $description = 'Fetch news from the API';

    public function handle()
    {
        $this->info('Fetching news from the API...');
        FetchMediaStack::dispatch(1);
        FetchGuardianNews::dispatch(1);
        $this->info('Done!');
    }
}
