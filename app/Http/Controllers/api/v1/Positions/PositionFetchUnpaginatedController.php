<?php

namespace App\Http\Controllers\api\v1\Positions;

use App\Http\Controllers\Controller;
use App\Http\Resources\api\v1\Positions\PositionResource;
use App\Models\Position;
use Illuminate\Http\Request;

class PositionFetchUnpaginatedController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke()
    {
        $positions = Position::query()
            ->select([
                'id',
                'name',
            ])
            ->orderBy('name')
            ->get();

        return PositionResource::collection($positions)->additional([
            'message' => 'Positions retrieved successfully.',
        ]);
    } 
}
