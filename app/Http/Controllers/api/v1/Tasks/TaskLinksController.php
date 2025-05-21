<?php

namespace App\Http\Controllers\api\v1\Tasks;

use App\Http\Controllers\Controller;
use App\Http\Requests\api\v1\Tasks\StoreTaskLinkRequest;
use App\Http\Requests\api\v1\tasks\UpdateTaskLinkRequest;
use App\Http\Resources\api\v1\tasks\LinkResource;
use App\Http\Resources\api\v1\Tasks\TaskResource;
use App\Models\Link;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskLinksController extends Controller
{
    public function store(StoreTaskLinkRequest $request, Task $task)
    {
        $task->links()->create($request->validated());

        return TaskResource::make($task->fresh()->load('links'))->additional([
            'message' => 'Task links created successfully.',
        ]);
    }

    public function update(Link $link, UpdateTaskLinkRequest $request)
    {
        $link->update($request->validated());

        return LinkResource::make($link->fresh());
    }

    public function destroy(Link $link)
    {
        $link->delete();

        return response()->noContent();
    }
}
