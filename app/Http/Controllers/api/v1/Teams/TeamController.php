<?php

namespace App\Http\Controllers\api\v1\Teams;

use App\Http\Controllers\api\v1\ApiController;
use App\Http\Requests\api\v1\Teams\StoreTeamRequest;
use App\Http\Resources\api\v1\Teams\TeamResource;
use App\Models\Team;
use App\Services\v1\TeamService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

class TeamController extends ApiController
{

    protected $teamService;

    public function __construct(TeamService $teamService)
    {
        $this->teamService = $teamService;
    }
    /**
     * List Teams
     * 
     * Retrieve a paginated list of teams with optional search and sorting functionality.
     * 
     * @group Team Management
     * 
     * @queryParam search string Filter teams by a search term in their name. This performs a partial match. Example: search=engineering
     * @queryParam column string The column to sort teams by. Allowed values: `title`, `description`, `priority_levels`, `status`, `deadline_at`, `started_at`, `completed_at`, `created_at`. Defaults to `created_at`. Example: column=name
     * @queryParam direction string The direction to sort teams by. Allowed values: `asc`, `desc`. Defaults to `desc`. Example: direction=asc
     */
    public function index(Request $request)
    {
        $column = $this->teamService
            ->getValidSortColumn(
                $request->query('column', 'created_at')
            );
        $direction = $this->teamService
            ->getValidSortDirection(
                $request->query('direction', 'desc')
            );
        $teams = Team::query()
            ->select(['id', 'name'])
            ->search($request->query('search'))
            ->orderBy($column, $direction)
            ->paginate(5);

       return TeamResource::collection($teams);
    }

    /**
     * Create Team
     * 
     * Store a newly created team in storage.
     * @group Team Management
     * @response 201 {  "data": {
        "id": 2,
        "name": "new team"
    }}
     * 
     */
    public function store(StoreTeamRequest $request)
    {

        $team = Team::create($request->validated());
       
        return new TeamResource($team);
    }

    /**
     * Retrieve Team
     * 
     * Display the specified team.
     * @group Team Management
     * @response 200 {  "data": {
        "id": 1,
        "name": "Enim a earum voluptate facilis cumque ex ut.",
        "members": [
            {
                "id": 2,
                "name": "Trey Bechtelar"
            },
            {
                "id": 3,
                "name": "Emerald Mertz"
            },
        ]
    }}
     */
    public function show(Team $team)
    {
        Gate::authorize('view', $team);
       
        return new TeamResource($team->load('members'));
    }

    /**
     * Update Team
     * 
     * Update the specified team in storage.
     * @group Team Management
     * @response 200 { "data": {
        "id": 1,
        "name": "updated team"
    }}
     * 
     */
    public function update(StoreTeamRequest $request, Team $team)
    {
        $team->update($request->validated());

        return new TeamResource($team);
    }

    /**
     * Delete Team
     * 
     * Remove the specified team storage.
     * @group Team Management
     * @response 200 {}
     */
    public function destroy(Team $team)
    {
        Gate::authorize('delete', $team);
        $team->delete();

        return $this->ok('');
    }
}
