<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;

class Article extends Model
{
    use HasFactory,SoftDeletes;
    protected $fillable = ['title', 'author', 'source_id', 'category_id', 'published_at', 'content', 'url','image'];
    public function source(): BelongsTo
    {
        return $this->belongsTo(Source::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public static function newsApiAiData($data): array
    {
        return [
            'categoryName' => $data['dataType'] ?? 'general',
            'url' => $data['url'] ?? 'general' . rand(1, 1000),
            'author' => array_key_exists('name', $data['authors']) ? $data['authors']['name'] : null,
            'title' => $data['title'],
            'published_at' => $data['date'],
            'content' => $data['body'],
        ];
    }

    public static function guardianNewsData($data): array
    {
        return [
            'categoryName' => $data['sectionName'],
            'url' => $data['webUrl'],
            'title' => $data['webTitle'],
            'published_at' => Carbon::parse($data['webPublicationDate'])->format('Y-m-d H:i:s'),
        ];
    }

    public static function newsApiData($data): array
    {
        return [
            'categoryName' => array_key_exists('name',$data['source']) ? $data['source']['name'] : 'general',
            'url' => $data['url'] ?? 'general'.rand(1,1000),
            'author' => $data['author'],
            'title' => $data['title'],
            'published_at' => Carbon::parse($data['publishedAt'])->format('Y-m-d H:i:s'),
            'content' => $data['content'],
        ];
    }

    public static function mediaStackData($data): array
    {
        Log::info($data);
        return [
            'content' => $data['description'],
            'url' => $data['url'],
            'author' => $data['author'],
            'category' => $data['category'],
            'image' => $data['image'],
            'title' => $data['title'],
            'published_at' => Carbon::parse($data['published_at'])->format('Y-m-d H:i:s'),
        ];
    }
}
