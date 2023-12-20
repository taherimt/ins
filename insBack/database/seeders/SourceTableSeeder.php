<?php

namespace Database\Seeders;

use App\Models\Source;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SourceTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'name' => 'The Guardian',
                'api_endpoint' => 'https://content.guardianapis.com/search',
                'api_key' => 'f642cc10-c30b-42f1-a329-16c87dce5b87',
            ],
            [
                'name' => 'The NewsApi',
                'api_endpoint' => 'https://newsapi.org/v2/everything',
                'api_key' => 'dce095683d1744a08503905df8a2a982',
            ],
            [
                'name' => 'Media Stack',
                'api_endpoint' => 'http://api.mediastack.com/v1/news?access_key=',
                'api_key' => '9ae3adfdad5d36108edc15e4ca5c1de1',
            ],
        ];

        // Insert data into the 'source' table
        foreach ($data as $sourceData) {
            Source::create($sourceData);
        }

    }
}
