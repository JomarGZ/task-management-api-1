<?php

namespace App\Http\Controllers\api\v1\Comments;

use App\Http\Controllers\Controller;
use App\Http\Requests\api\v1\Comments\StoreCommentRequest;
use App\Http\Requests\api\v1\Comments\UpdateCommentRequest;
use App\Http\Resources\api\v1\Comments\CommentResource;
use App\Models\Comment;

class CommentController extends Controller
{
    public function store(StoreCommentRequest $request)
    {
        $comment = request()->user()->comments()->create($request->validated());

        return  CommentResource::make($comment->load(['user:id,name,position', 'user.media']))->additional(['message' => 'Comment created Successfully']);
    }

    public function update(UpdateCommentRequest $request, Comment $comment)
    {
        
        $comment->update($request->validated());

        return CommentResource::make($comment->fresh())->additional(['message' => 'Comment updated successfully']);
    }

    public function destroy(Comment $comment)
    {
        $comment->delete();

        return response()->noContent();
    }

}
