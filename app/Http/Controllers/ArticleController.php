<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Articles",
 *     description="API Endpoints for Articles"
 * )
 */
class ArticleController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/articles",
     *     summary="Get list of articles",
     *     tags={"Articles"},
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="integer"),
     *         description="Page number for pagination"
     *     ),
     *     @OA\Parameter(
     *         name="keyword",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string"),
     *         description="Keyword to search articles"
     *     ),
     *     @OA\Response(response=200, description="Success"),
     *     @OA\Response(response=400, description="Bad Request")
     * )
     */
    public function index(Request $request)
    {
        $query = Article::query();

        // Apply filters for search, category, and source
        if ($request->filled('keyword')) {
            $query->where('title', 'like', "%{$request->keyword}%");
        }
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        if ($request->filled('source')) {
            $query->where('source', $request->source);
        }

        // Fetch articles with pagination
        return $query->paginate(10);
    }

    /**
     * @OA\Get(
     *     path="/api/articles/{id}",
     *     summary="Get details of a single article",
     *     tags={"Articles"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID of the article"
     *     ),
     *     @OA\Response(response=200, description="Success"),
     *     @OA\Response(response=404, description="Article not found")
     * )
    */
    public function show($id)
    {
        return Article::findOrFail($id);
    }
}
