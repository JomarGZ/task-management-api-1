<?php

namespace App\Events;

use App\Http\Resources\api\v1\Comments\CommentResource;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CommentCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $comment;
    public $user;
    /**
     * Create a new event instance.
     */
    public function __construct(Comment $comment, User $user = null)
    {
        $this->comment = $comment;
        $this->user = $user ??= auth()->user();
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('task.comment.created'),
        ];
    }

    public function broadcastWith()
    {
        $comment = $this->comment->load(['user:id,name', 'replies:id,content,created_at']);
        return [
            'data' => [
                'id' => $comment->id,
                'content' => $comment->content,
                'created_at' => $comment->created_at,
                'updated_at' => $comment->updated_at,
                'author' => [
                    'id' => $comment->user->id,
                    'name' => $comment->user->name,
                ],
                'replies' => []
            ]
        ];
    }
}
