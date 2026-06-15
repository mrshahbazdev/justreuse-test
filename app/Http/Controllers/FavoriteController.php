<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TblSavedPosts;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    /**
     * Add or remove an ad from the user's favorites.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggle(Request $request)
    {
        $request->validate([
            'post_id' => 'required|exists:tbl_posts,id',
        ]);

        if (!Auth::check()) {
            return response()->json(['status' => 'error', 'message' => 'Please login first.'], 401);
        }

        $userId = Auth::id();
        $postId = $request->post_id;

        $favorite = TblSavedPosts::where('user_id', $userId)
                                 ->where('post_id', $postId)
                                 ->first();

        if ($favorite) {
            // If it exists, delete it (unfavorite)
            $favorite->delete();
            return response()->json([
                'status' => 'success',
                'favorited' => false,
                'message' => 'Ad removed from favorites.'
            ]);
        } else {
            // If it doesn't exist, create it (favorite)
            TblSavedPosts::create([
                'user_id' => $userId,
                'post_id' => $postId,
            ]);
            return response()->json([
                'status' => 'success',
                'favorited' => true,
                'message' => 'Ad added to favorites.'
            ]);
        }
    }
}

