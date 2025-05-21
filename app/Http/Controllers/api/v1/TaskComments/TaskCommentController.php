<?php

namespace App\Http\Controllers\api\v1\TaskComments;

use App\Http\Controllers\api\v1\ApiController;
use App\Http\Requests\api\v1\TaskComment\StoreTaskCommentRequest;
use App\Http\Resources\api\v1\Comments\CommentResource;
use App\Http\Resources\api\v1\TaskComment\TaskCommentResource;
use App\Models\Comment;
use App\Models\Task;
use App\Services\v1\TaskCommentService;

class TaskCommentController extends ApiController 
{
  
    public function index(Task $task)
    {
        $comments = Comment::select([
            'id',
            'commentable_id', 
            'commentable_type', 
            'content', 
            'user_id', 
            'created_at'
            ])->forModel($task)
            ->with([
                'user:id,name',
                'user.media' 
            ])
            ->withReplies()
            ->topLevelOnly()
            ->latest()
            ->paginate(request('per_page', 2));

        return CommentResource::collection($comments)->additional(['message' => 'Retrieved Task Comment Successfully']);
    }

}
