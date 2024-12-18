<?php

namespace App\Http\Controllers\api\v1\Tasks;

use App\Http\Controllers\Controller;
use App\Http\Resources\api\v1\Tasks\TaskResource;
use App\Models\Task;
use App\Utilities\ApiResponse;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $tasks = Task::query()
            ->select([
                'id',
                'title', 
                'description',
                'priority_level',
                'status',
                'deadline_at',
                'started_at',
                'completed_at'
            ])
            ->search($request->query('search'))
            ->filterByStatus($request->query('status'))
            ->filterByPriorityLevel($request->query('priority_level'))
            ->orderBy(
                $request->query('column', 'created_at'), 
                $request->query('direction', 'desc')
            )
            ->paginate(5);

        
        return ApiResponse::success(
            TaskResource::collection($tasks)->response()->getData(true),
            'Tasks retrieved successfully'
        );

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Task $task)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        //
    }
}
