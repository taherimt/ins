<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Jobs\Guardian\FetchGuardianNews;
use App\Jobs\MediaStack\FetchMediaStack;
use App\Models\Article;
use App\Models\Category;
use App\Models\Setting;
use App\Models\Source;
use App\Repositories\ArticleRepository;
use Illuminate\Http\Request;

class FetchArticleController extends Controller
{
    public function __construct(protected ArticleRepository $articleRepository)
    {
    }

    public function fetch()
    {
        FetchMediaStack::dispatch(1);
        FetchGuardianNews::dispatch(1);

        return response()->json('Success');
    }


    public function search()
    {

        $request = request();

        $articles = $this->articleRepository->searchArticle($request->input('search'));
        return response()->json($articles);
    }

    public function index(Request $request)
    {
        FetchMediaStack::dispatch(1)->onQueue('default');
        FetchGuardianNews::dispatch(1)->onQueue('default');

        $query = Article::with(['source', 'category']);

        $user = $request->input('user');
        $sources = Setting::select('name as api')->where(['user_id' => $user, 'type' => 'source'])->get()->pluck('api')->toArray();
        $authors = Setting::select('name as authors')->where(['user_id' => $user, 'type' => 'author'])->get()->pluck('authors')->toArray();
        $categories = Setting::select('name as categories')->where(['user_id' => $user, 'type' => 'category'])->get()->pluck('categories')->toArray();

        if ($user) {
            // Check if $sources is not empty before applying the filter
            if (!empty($sources)) {
                $query->whereIn('source_id', $sources);
            }

            // Check if $categories is not empty before applying the filter
            if (!empty($categories)) {
                $query->whereIn('category_id', $categories);
            }

            // Check if $authors is not empty before applying the filter
            if (!empty($authors)) {
                $query->whereIn('author', $authors);
            }
        }

        if ($date = $request->input('date')) {
            $query->whereDate('published_at', $date);
        }

        if ($author = $request->input('author')) {
            $query->where('author', $author);
        }

        if ($source = $request->input('source')) {
            $query->where('source_id', $source);
        }

        if ($category = $request->input('category')) {
            $query->where('category_id', $category);
        }

        if ($request->input('s')) {
            $this->articleRepository->searchArticle($request->input('s'));
            $query->where('title', 'LIKE', '%' . $request->input('s') . '%')->orWhere('content', 'LIKE', '%' . $request->input('s') . '%');
        }

        if ($sort = $request->input('sort')) {
            $query->orderBy('id', $sort);
        }

        $total = $query->count();
        $perPage = 24;
        $page = $request->input('page', 1);

        $articles = $query->offset(($page - 1) * $perPage)->limit($perPage)->get();

        return response()->json([
            'articles' => $articles,
            'total' => $total,
            'page' => $page,
            'lastPage' => ceil($total / $perPage)
        ], 200);
    }

    public function customizeInfo()
    {
        $sources = Source::select('id', 'name')->distinct()->get();
//        $sources = Article::get();
        $authors = Article::select('author')
            ->whereNotNull('author')
            ->distinct()
            ->get();
        $categories = Category::select('id', 'name')->distinct()->get();

        return response()->json([
            'sources' => $sources,
            'authors' => $authors,
            'categories' => $categories,
        ], 200);
    }

    public function checkSetting(Request $request)
    {
        $user_id = $request->input('user');

        $uniqueSources = Setting::with('source')
            ->where(['user_id' => $user_id, 'type' => 'source'])
            ->distinct()
            ->get()
            ->pluck('source.name')
            ->toArray();

        $uniqueAuthors = Setting::select('name')
            ->where(['user_id' => $user_id, 'type' => 'author'])
            ->distinct()
            ->get()
            ->toArray();

        $uniqueCategories = Setting::with('category')
            ->where(['user_id' => $user_id, 'type' => 'category'])
            ->distinct()
            ->get()
            ->pluck('category.name')
            ->toArray();

        return response()->json([
            'sources' => $uniqueSources,
            'authors' => $uniqueAuthors,
            'categories' => $uniqueCategories
        ], 200);
    }


    public function storeSetting(Request $request)
    {
        $userId = $request->input('user_id');

        // Process sources
        $sources = $request->input('sources');
        $this->processSettings($userId, 'source', $sources);

        // Process authors
        $authors = $request->input('authors');
        $this->processSettings($userId, 'author', $authors);

        // Process categories
        $categories = $request->input('categories');
        $this->processSettings($userId, 'category', $categories);

        $res = ['status' => 'success'];
        return response($res);
    }

    private function processSettings($userId, $type, $newSettings)
    {
        if (empty($newSettings)) {
            // If the new settings list is empty, remove all settings of this type for the user
            Setting::where('user_id', $userId)->where('type', $type)->delete();
        } else {
            // Remove settings that are not in the new settings list
            Setting::where('user_id', $userId)
                ->where('type', $type)
                ->whereNotIn('name', $newSettings)
                ->delete();

            foreach ($newSettings as $newSetting) {
                // Create new setting if it doesn't exist
                Setting::firstOrCreate(
                    ['user_id' => $userId, 'type' => $type, 'name' => $newSetting]
                );
            }
        }
    }

}
