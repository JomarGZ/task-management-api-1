<?php

namespace App\Http\Controllers\api\v1\Chats;

use App\Http\Controllers\Controller;
use App\Http\Resources\api\v1\Chats\MessageResource;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MessageLikesController extends Controller
{
    public function store(Message $message, Request $request)
    {
        $user = $request->user();

        try {
            $result = DB::transaction(function() use ($message, $user) {
                $alreadyLiked = $message->likes()->where('user_id', $user->id)->exists();
                if ($alreadyLiked){
                    $message->likes()->detach($user->id);
                    $message->decrement('reaction_count');
                } else {
                    $message->likes()->attach($user->id, ['tenant_id' => $user->tenant_id]);
                    $message->increment('reaction_count');
                }
                return ['liked' => !$alreadyLiked, 'count' => $message->reaction_count];
            });
            return MessageResource::make($message)->additional([
                'message' => $result['liked'] ? 'Liked message successfully' : 'Unliked message successfully',
                'liked' => $result['liked'],
                'reaction_count' => $message->fresh()->reaction_count
            ]);

            
        } catch (\Exception $e) {
            Log::error("Failed to toggle like: " . $e->getMessage(), [
                'user_id' => $user->id,
                'message_id' => $message->id
            ]);
            
            return response()->json([
                'message' => 'Failed to process like',
                'error' => $e->getMessage()
            ], 500);
        }
       
    }
}
