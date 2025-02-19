<?php
namespace App\Services\v1;

use App\Models\Task;

class TaskCommentService {
    public function getTaskComments(Task $task)
    {
        
        return $task->comments()
            ->select(['id', 'content', 'created_at', 'commentable_type', 'commentable_id', 'author_id'])
            ->where('commentable_type', Task::class)
            ->with([
                'author:id,name',
                'replies' => function ($query) {
                    $query->select(['id', 'content', 'created_at', 'author_id', 'commentable_type', 'commentable_id'])
                        ->orderBy('created_at', 'desc')
                        ->with('author:id,name');
                }
            ])
            ->orderBy('created_at', 'desc')
            ->get();
    }
}