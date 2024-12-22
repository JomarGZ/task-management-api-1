<?php

namespace App\Http\Controllers\api\v1\Teams;

use App\Http\Controllers\Controller;
use App\Http\Requests\api\v1\Teams\StoreTeamRequest;
use App\Http\Resources\api\v1\Teams\TeamResource;
use App\Models\Team;
use App\Utilities\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

class TeamController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $teams = Team::query()
            ->select(['id', 'name'])
            ->search($request->query('search'))
            ->orderBy($request->query('column', 'created_at'), $request->query('direction', 'desc'))
            ->paginate(5);

        return ApiResponse::success(
            TeamResource::collection($teams)->response()->getData(true),
            'Teams retrieved successfully'
        );

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTeamRequest $request)
    {

        $team = Team::create($request->validated());
        return ApiResponse::success(
            TeamResource::make($team),
            'Team created successfully',
            Response::HTTP_CREATED
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Team $team)
    {
        Gate::authorize('view', $team);
        return ApiResponse::success(
            TeamResource::make($team->load('members')),
            'Team retrieved succssfully'
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreTeamRequest $request, Team $team)
    {
        $team->update($request->validated());

        return ApiResponse::success(
            TeamResource::make($team),
            'Team updated successfully',
            Response::HTTP_OK
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Team $team)
    {
        Gate::authorize('delete', $team);
        $team->delete();

        return response()->noContent();
    }
}
