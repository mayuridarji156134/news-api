<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\UserPreference;
use Illuminate\Http\Request;

class UserPreferenceController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/preferences",
     *     summary="Set user preferences",
     *     tags={"User Preferences"},
     *     security={{ "bearerAuth": {} }},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="preferred_sources", type="array", @OA\Items(type="string")),
     *             @OA\Property(property="preferred_categories", type="array", @OA\Items(type="string")),
     *         )
     *     ),
     *     @OA\Response(response=200, description="Preferences set successfully", @OA\JsonContent()),
     *     @OA\Response(response=422, description="Validation error", @OA\JsonContent()),
     *     @OA\Response(response=401, description="Unauthorized", @OA\JsonContent())
     * )
     */
    public function setPreferences(Request $request)
    {
        $request->validate([
            'preferred_sources' => 'array',
            'preferred_categories' => 'array',
        ]);

        $preferences = UserPreference::updateOrCreate(
            ['user_id' => $request->user()->id],
            [
                'preferred_sources' => json_encode($request->preferred_sources),
                'preferred_categories' => json_encode($request->preferred_categories),
            ]
        );

        return response()->json($preferences);
    }

    /**
     * @OA\Get(
     *     path="/api/preferences",
     *     summary="Get user preferences",
     *     tags={"User Preferences"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(response=200, description="User preferences retrieved successfully", @OA\JsonContent()),
     *     @OA\Response(response=404, description="Preferences not found", @OA\JsonContent()),
     *     @OA\Response(response=401, description="Unauthorized", @OA\JsonContent())
     * )
     */
    public function getPreferences(Request $request)
    {
        $preferences = UserPreference::where('user_id', $request->user()->id)->first();

        if ($preferences) {
            return response()->json($preferences);
        }

        return response()->json(['message' => 'Preferences not found'], 404);
    }

    /**
     * @OA\Get(
     *     path="/api/personalized-feed",
     *     summary="Get personalized feed based on user preferences",
     *     tags={"User Preferences"},
     *     security={{ "bearerAuth": {} }},
     *     @OA\Response(response=200, description="Personalized feed retrieved successfully", @OA\JsonContent()),
     *     @OA\Response(response=401, description="Unauthorized", @OA\JsonContent())
     * )
     */
    public function personalizedFeed(Request $request)
    {
        $preferences = $request->user()->preferences;
        $query = Article::query();

        if ($preferences) {
            if ($preferences->preferred_sources) {
                $sources = json_decode($preferences->preferred_sources);
                $query->whereIn('source', $sources);
            }
            if ($preferences->preferred_categories) {
                $categories = json_decode($preferences->preferred_categories);
                $query->whereIn('category', $categories);
            }
        }

        return response()->json($query->paginate(10));
    }
}
