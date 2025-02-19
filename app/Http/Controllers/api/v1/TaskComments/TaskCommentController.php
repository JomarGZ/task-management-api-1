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
    protected $taskCommentService;
    public function __construct(TaskCommentService $taskCommentService)
    {
        $this->taskCommentService = $taskCommentService;
    }
    public function index(Task $task)
    {
        $taskComments = $this->taskCommentService->getTaskComments($task);

        return CommentResource::collection($taskComments);
    }
    /**
     * Create task comment
     * 
     * @group Task Comments Management
     * 
     * @urlParam task required The ID of the task whose comment is to be created. Example: 1
     * @requires content
     * @response 201 {  "data": {
        "id": 7,
        "content": "new comment",
        "created_at": "2025-01-03T05:27:32.000000Z",
        "updated_at": "2025-01-03T05:27:32.000000Z"
    }}
     */
    public function store(StoreTaskCommentRequest $request, Task $task)
    {
        $taskNewComment = $task->comments()->create([
            'content' => $request->content
        ]);
        return new TaskCommentResource($taskNewComment);
    }

    /**
     * Retrieve Task Comments
     * 
     * @group Task Comments Management
     * 
     * @urlParam comment required The ID of the comment to be retrieved. Example: 1
     * @response 200 {  "data": {
        "id": 7,
        "content": "new comment",
        "created_at": "2025-01-03T05:27:32.000000Z",
        "updated_at": "2025-01-03T05:27:32.000000Z"
     */
    public function show(Comment $comment)
    {
        return new TaskCommentResource($comment);
    }

    /**
     * Update Task Comment
     * 
     * @group Task Comments Management
     * @requires content
     * @urlParam comment required The ID of the comment to be updated. Example: 1
     * 
     * @response 200 {  "data": {
        "id": 7,
        "content": "update comment",
        "created_at": "2025-01-03T05:27:32.000000Z",
        "updated_at": "2025-01-03T05:27:32.000000Z"
     */
    public function update(StoreTaskCommentRequest $request, Comment $comment)
    {
        $comment->update([
            'content' => $request->content
        ]);
        return new TaskCommentResource($comment);
    }

    /**
     * Delete Task Comment
     * 
     * @group Task Comments Management
     * @urlParam comment required The ID of the comment to be deleted. Example: 1
     * 
     * @response 200 {  "message": "Success" }
     */
    public function destroy(Comment $comment)
    {
        $comment->delete();
        return $this->ok('');
    }
}
