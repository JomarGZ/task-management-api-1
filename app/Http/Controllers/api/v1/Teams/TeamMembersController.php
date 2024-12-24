<?php

namespace App\Http\Controllers\api\v1\Teams;

use App\Http\Controllers\api\v1\ApiController;
use App\Http\Requests\api\v1\Teams\StoreTeamMemberRequest;
use App\Http\Resources\api\v1\Teams\TeamMemberResource;
use App\Http\Resources\api\v1\Teams\TeamResource;
use App\Models\Team;
use App\Models\User;
use App\Utilities\ApiResponse;
use Illuminate\Http\Response;
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
        $team->load('members');
        
        return TeamMemberResource::collection($team->members);
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
        }
    ]}
     * 
     */
    public function store(StoreTeamMemberRequest $request, Team $team)
    {
        $memberData = collect($request->member_id)
            ->mapWithKeys(fn ($id) => [
                $id => ['tenant_id' => $request->user()->tenant_id]
            ])->all();
    
        $team->members()->syncWithoutDetaching($memberData);
    
        $addedMembers = $team->members()->whereIn('users.id', $request->member_id)->get();
    
        return TeamMemberResource::collection($addedMembers);
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
