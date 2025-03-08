<?php

namespace App\Http\Controllers\api\v1\Tenants;

use App\Http\Controllers\api\v1\ApiController;
use App\Http\Requests\api\v1\TenantMembers\AddMemberRequest;
use App\Http\Requests\api\v1\TenantMembers\UpdateMemeberRequest;
use App\Http\Resources\api\v1\tenants\TenantMemberResource;
use App\Models\User;
use App\Services\v1\TenantMemberService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;

class TenantMembersController extends ApiController
{
    protected $tenantMemberService;

    public function __construct(TenantMemberService $tenantMemberService)
    {
        $this->tenantMemberService = $tenantMemberService;
    }
    /**
     * List Tenant Members
     * 
     * Display a listing of the tenant members. This endpoint supports sorting, searching, and filtering by role.
     * 
     * @group Tenant Members Management
     * 
     * @queryParam column string The column to sort by. Valid columns are: name, email, created_at. Default is created_at. Example: column=name
     * @queryParam direction string The direction to sort. Either 'asc' or 'desc'. Default is 'desc'. Example: direction=asc
     * @queryParam search string A search term to filter members by name or email. Example: search=johndoe
     * @queryParam role string Filter by role. Example: role=admin
     * 
     */
    public function index(Request $request)
    {
        $direction = $this->tenantMemberService
            ->getValidSortDirection(
                $request->query('direction', 'desc')
            );
        $column = $this->tenantMemberService
            ->getValidSortColumn(
                $request->query('column', 'created_at')
            );
        
        $members = User::query()
            ->select(['id', 'name', 'role', 'email'])
            ->with(['media'])
            ->search($request->query('search')) 
            ->filterByRole($request->query('role'))
            ->orderBy(
                $column,
                $direction)
            ->paginate(10);

        return TenantMemberResource::collection($members);
    }

    /**
     * Create Tenant member
     * 
     * Store a newly created Tenant member in storage.
     * @group Tenant Members Management
     * @response 201 { "data": {
        "id": 14,
        "name": "adasdsdaasdsad",
        "email": "dasADda@gmail.com",
        "role": "member"
    }}
     */
    public function store(AddMemberRequest $request)
    {
        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
        ];
       $newMember = $this->tenantMemberService->addMember($data);
       return new TenantMemberResource($newMember);
    }

    /**
     * Retrieve Tenant Member
     * 
     * Display the specified tenant member.
     * @group Tenant Members Management
     * @response 200 {   "data": {
        "id": 14,
        "name": "adasdsdaasdsad",
        "email": "dasADda@gmail.com",
        "role": "member"
    }}
     */
    public function show(User $user)
    {
       return new TenantMemberResource($user->load('media'));
    }

    /**
     * Update Tenant Member
     * 
     * Update the specified tenant member in storage.
     * @group Tenant Members Management
     * @response 200 {   "data": {
        "id": 14,
        "name": "adasdsdaasdsad",
        "email": "dasADda@gmail.com",
        "role": "member"
    }}
     * 
     */
    public function update(UpdateMemeberRequest $request, User $user)
    {
        $user->update($request->validated());
        return new TenantMemberResource($user);
    }

    /**
     * Delete Tenant Member
     * 
     * Remove the specified tenant member from storage.
     * @group Tenant Members Management
     * @response 200 {}
     */
    public function destroy(User $user)
    {
        Gate::authorize('delete', $user);
        $user->delete();
        return response()->noContent();
    }
}
