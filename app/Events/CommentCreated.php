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
    protected $authId;
    /**
     * Create a new event instance.
     */
    public function __construct(Comment $comment)
    {
        $this->comment = $comment->load(['user:id,name,email,position', 'replies:id,content,created_at', 'user.media']);
        $this->authId = auth()->id();
    }

    public function broadcastOn(): array
    {
        return [
             new Channel('task.'.$this->comment->commentable_id.'.comments'),
        ];
    }

    public function broadcastWith()
    {
        return [
            'data' => new CommentResource($this->comment),
            'task_id' => $this->comment->commentable_id,
        ];
    }
}
