<?php

namespace App\Http\Controllers\api\v1\Tasks;

use App\Http\Controllers\Controller;
use App\Http\Resources\api\v1\Tasks\TaskResource;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskLinksController extends Controller
{
    public function update(Request $request, Task $task)
    {
        $validatedData = $request->validate([
            'pr_link' => 'nullable|url',
            'issue_link' => 'nullable|url',
            'doc_link' => 'nullable|url',
            'other_link' => 'nullable|url',
        ]);

        $task->update($validatedData);

        return TaskResource::make($task)->additional([
            'message' => 'Task links updated successfully.',
        ]);
    }
}
