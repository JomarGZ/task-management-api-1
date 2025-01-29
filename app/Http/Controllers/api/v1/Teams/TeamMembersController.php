<?php

namespace App\Http\Controllers\api\v1\Teams;

use App\Http\Controllers\api\v1\ApiController;
use App\Http\Requests\api\v1\Teams\StoreTeamMemberRequest;
use App\Http\Resources\api\v1\Teams\TeamMemberResource;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class TeamMembersController extends ApiController
{
    /**
     * List Team Members
     * 
     * Display a listing of the team members.
     * @group Team Members Management
     */
    public function index(Team $team)
    {
        $members = $team->members()
        ->where('users.tenant_id', auth()->user()->tenant_id)
        ->get();
        
        return TeamMemberResource::collection($members);
    }

    /**
     * Create Team Member
     * 
     * Store a newly created team member in storage.
     * @group Team Members Management
     * @response 200 {  "data": [
        {
            "id": 2,
            "name": "Trey Bechtelar"
            "role": "member"
        }
    ]}
     * 
     */
    public function store(StoreTeamMemberRequest $request, Team $team)
    {
        $team->members()->attach($request->member_id, ['role' => $request->role, 'tenant_id' => auth()->user()->tenant_id]);
        $addedMembers = $team->members()->where('users.id', $request->member_id)->first();
        return TeamMemberResource::make($addedMembers);
    }

    /**
     * Delete Team Member
     * 
     * Remove the specified team member from storage.
     * @group Team Members Management
     * @response 200 {}
    */
    public function destroy(Team $team, User $user)
    {
        $teamUser = $team->members()->where('member_id', $user->id)->firstOrFail();

        Gate::authorize('delete', $teamUser);
        $team->members()->detach($user->id);

        return response()->noContent();
    }
}
