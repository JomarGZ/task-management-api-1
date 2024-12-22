<?php

namespace App\Http\Controllers\api\v1\Teams;

use App\Http\Controllers\Controller;
use App\Http\Requests\api\v1\Teams\StoreTeamMemberRequest;
use App\Http\Resources\api\v1\Teams\TeamResource;
use App\Models\Team;
use App\Models\User;
use App\Utilities\ApiResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

class TeamMembersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Team $team)
    {
        $team->load('members');
        return ApiResponse::success(
            [
                'team' => TeamResource::make($team)
            ],
            'Team members retrieved successfully'
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTeamMemberRequest $request, Team $team)
    {
        $memberData = collect($request->member_id)
            ->mapWithKeys(fn ($id) => [
                $id => ['tenant_id' => $request->user()->tenant_id]
            ])->all();

        $team->members()->syncWithoutDetaching($memberData);

        return ApiResponse::success(
            TeamResource::make($team->load('members')),
            'Member(s) added to the team succssfully',
            Response::HTTP_OK
        );
    }

    /**
     * Remove the specified resource from storage.
    */
    public function destroy(Team $team, User $user)
    {
        $teamUser = $team->members()->where('member_id', $user->id)->firstOrFail();

        Gate::authorize('delete', $teamUser);
        $team->members()->detach($user->id);

        return response()->noContent();
    }
}
