<?php

namespace App\Http\Controllers\api\v1\Position;

use App\Http\Controllers\Controller;
use App\Http\Requests\api\v1\Positions\StorePositionRequest;
use App\Http\Requests\api\v1\Positions\UpdatePositionRequest;
use App\Http\Resources\api\v1\Positions\PositionResource;
use App\Models\Position;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class PositionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $positions = Position::query()
            ->select([
                'id',
                'name',
            ])
            ->latest()
            ->paginate(5);
        return PositionResource::collection($positions)->additional([
            'message' => 'Positions retrieved successfully.',
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePositionRequest $request)
    {
        Position::create($request->validated());

        return PositionResource::make($request)->additional([
            'message' => 'Position created successfully.',
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Position $position)
    {
        return PositionResource::make($position)->additional([
            'message' => 'Position retrieved successfully.',
        ]);
    }
  

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePositionRequest $request, Position $position)
    {
        $position->update($request->validated());

        return PositionResource::make($position)->additional([
            'message' => 'Position updated successfully.',
        ]);
    }
   

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Position $position)
    {
        Gate::authorize('delete', $position);
        $position->delete();

        return response()->json([
            'message' => 'Position deleted successfully.',
        ]);
    }

}
