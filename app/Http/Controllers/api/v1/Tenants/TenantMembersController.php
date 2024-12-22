<?php

namespace App\Http\Controllers\api\v1\Tenants;

use App\Http\Controllers\Controller;
use App\Http\Requests\api\v1\TenantMembers\AddMemberRequest;
use App\Http\Requests\api\v1\TenantMembers\FilterTenantMemberRequest;
use App\Http\Requests\api\v1\TenantMembers\UpdateMemeberRequest;
use App\Http\Resources\api\v1\tenants\TenantMemberResource;
use App\Models\User;
use App\Utilities\ApiResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;

class TenantMembersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(FilterTenantMemberRequest $request)
    {
        $members = User::query()
            ->select(['id', 'name', 'role', 'email'])
            ->search($request->query('search')) 
            ->filterByRole($request->query('role'))
            ->orderBy(
                $request->query('column', 'created_at'),
                $request->query('direction', 'desc'))
            ->paginate(2);

        return ApiResponse::success( 
            TenantMemberResource::collection($members)->response()->getData(true),
            'Successfully retrieved the members data'
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AddMemberRequest $request)
    {
        $newMember = User::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'role'      => $request->role,
            'password'  => Hash::make('password'),
        ]);

        return ApiResponse::success(
            $newMember,
        'New member added successfully',
        Response::HTTP_CREATED
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        return ApiResponse::success(
            TenantMemberResource::make($user),
            'Member retrieved successfully'
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMemeberRequest $request, User $user)
    {
        $user->update($request->validated());

        return ApiResponse::success(
            TenantMemberResource::make($user),
            'Member updated successfully'
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        Gate::authorize('delete', $user);
        $user->delete();
        return response()->noContent();
    }
}
