<?php

namespace App\Policies;

use App\Models\TeamUser;
use App\Models\User;

class TeamUserPolicy
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
    public function view(User $user, TeamUser $teamUser): bool
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // return $user->isAdmin();
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, TeamUser $teamUser): bool
    {
        return $user->isAdmin() && $user->belongsToTenant($teamUser);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, TeamUser $teamUser): bool
    {
        return $user->isAdmin() 
            || ($user->belongsToTenant($teamUser->id) && $user->isTeamLead($teamUser->id));
    }

   
}
