<?php

namespace App\Observers\v1;

use App\Events\CommentCreated;
use App\Events\TaskCommentAdded;
use App\Http\Resources\api\v1\Comments\CommentResource;
use App\Models\Comment;

class CommentObserver
{
    public function created(Comment $comment)
    {
        CommentCreated::dispatch($comment);
    }
}
