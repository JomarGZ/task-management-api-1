<?php
namespace App\Services\v1;

use App\Models\Task;

class TaskCommentService {
    public function getTaskComments(Task $task)
    {
        
        return $task->comments()
            ->select(['id', 'content', 'created_at', 'commentable_type', 'commentable_id', 'author_id', 'parent_id'])
            ->with([
                'author:id,name',
                'author.media',
                'replies' => function ($query) {
                    $query->select(['id', 'content', 'created_at', 'author_id', 'commentable_type', 'commentable_id', 'parent_id'])
                        ->orderBy('created_at', 'desc')
                        ->with('author:id,name');
                }
            ])
            ->whereNull('parent_id') // Only fetch top-level comments (not replies)
            ->orderBy('created_at', 'desc')
            ->get();
    }
}