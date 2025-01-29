<?php

namespace App\Http\Controllers\api\v1\Tasks;

use App\Http\Controllers\api\v1\ApiController;
use App\Http\Requests\api\v1\Tasks\UpdateTaskAssignmentRequest;
use App\Http\Resources\api\v1\Tasks\TaskResource;
use App\Models\Task;

class ProjectTaskDevAssignmentController extends ApiController
{
    /**
     * assign or reassign a developer to a task
     * 
     * @group Task Management
     * @param \App\Http\Requests\api\v1\Tasks\UpdateTaskAssignmentRequest $request
     * @queryParams assigned_dev_id int The ID of the developer to assign the task to. Example: 1
     * @queryParams assigned_qa_id int The ID of the QA to assign the task to. Example: 2 
     * @response 200 {   "data": {
        "id": 2,
        "title": "Eum harum aliquid qui beatae inventore quo recusandae.",
        "description": "I'll try if I shall remember it in large letters. It was all ridges and furrows; the balls were live hedgehogs, the mallets live flamingoes, and the Hatter continued, 'in this way:-- \"Up above the world am I? Ah, THAT'S the great puzzle!' And she began fancying the sort of thing never happened, and now here I am so VERY much out of this ointment--one shilling the box-- Allow me to introduce some other subject of conversation. 'Are you--are you fond--of--of dogs?' The Mouse only growled in.",
        "priority_level": "low",
        "status": "completed",
        "deadline_at": "2025-01-14",
        "started_at": "2025-01-10",
        "completed_at": "2025-01-12",
        "created_at": "2025-01-10T08:29:13.000000Z",
        "project": {
            "id": 1,
            "name": "Odio et impedit error et veniam quam.",
            "description": "I'll be jury,\" Said cunning old Fury: \"I'll try the first to speak. 'What size do you know about it, you know.' 'Not at all,' said Alice: 'besides, that's not a moment like a serpent. She had.",
            "created_at": null
        },
        "assigned_dev": {
            "id": 2,
            "name": "Evalyn Conroy"
        },
        "assigned_qa": null
    }}
     */
    public function store(UpdateTaskAssignmentRequest $request, Task $task)
    {
        if ($request->has('assigned_dev_id')) {
            $task->update(['assigned_dev_id' => $request->assigned_dev_id]);
        }
        return new TaskResource(
            $task->load([
                'project:id,name,description',
                'assignedDev:id,name,email',
                'assignedQA:id,name,email'
                ])
        );
    }

    /**
     * unassign a developer from a task
     * 
     * @group Task Management
     * @param \App\Models\Task $task
     * @response 200 {"data": {
        "id": 2,
        "title": "Eum harum aliquid qui beatae inventore quo recusandae.",
        "description": "I'll try if I shall remember it in large letters. It was all ridges and furrows; the balls were live hedgehogs, the mallets live flamingoes, and the Hatter continued, 'in this way:-- \"Up above the world am I? Ah, THAT'S the great puzzle!' And she began fancying the sort of thing never happened, and now here I am so VERY much out of this ointment--one shilling the box-- Allow me to introduce some other subject of conversation. 'Are you--are you fond--of--of dogs?' The Mouse only growled in.",
        "priority_level": "low",
        "status": "completed",
        "deadline_at": "2025-01-14",
        "started_at": "2025-01-10",
        "completed_at": "2025-01-12",
        "created_at": "2025-01-10T08:29:13.000000Z",
        "project": {
            "id": 1,
            "name": "Odio et impedit error et veniam quam.",
            "description": "I'll be jury,\" Said cunning old Fury: \"I'll try the first to speak. 'What size do you know about it, you know.' 'Not at all,' said Alice: 'besides, that's not a moment like a serpent. She had.",
            "created_at": null
        },
        "assigned_dev": null,
        "assigned_qa": null
    }
}}
     */
    public function destroy(Task $task)
    {
        if (!is_null($task->assigned_dev_id)) {
            $task->update(['assigned_dev_id' => null]);
           
        } 
        return new TaskResource(
            $task->load([
                'project:id,name,description',
                'assignedDev:id,name,email',
                'assignedQA:id,name,email'
                ])
        );
    }
}
