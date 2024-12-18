<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TaskPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Task $task): bool
    {
        return $user->belongsToTenant($task);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Task $task): bool
    {
        return $user->isAdmin() && $user->belongsToTenant($task);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Task $task): bool
    {
        return $user->isAdmin() && $user->belongsToTenant($task);
    }

    public function updateStatus(User $user, Task $task)
    {
        return $user->belongsToTenant($task);
    }
    public function updatePriorityLevel(User $user, Task $task)
    {
        return $user->isAdmin()->$user->belongsToTenant($task);
    }
    public function updateEstimateToFinish(User $user, Task $task)
    {
        return $user->belongsToTenant($task);
    }
}
