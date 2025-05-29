<?php

namespace App\Http\Controllers\api\v1\Notifications;

use App\Http\Controllers\Controller;
use App\Http\Resources\api\v1\Notifications\NotificationResource;
use Illuminate\Http\Request;

class NotificationDropDownController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $query = $user->unreadNotifications();
        $limitedNotifications = $query->clone()
            ->latest()
            ->limit(5)
            ->get();
        $unreadNotifications = $query->clone()->count();
        return NotificationResource::collection($limitedNotifications)
            ->additional([
                'message' => 'Unread notifications retrieved successfully',
                'unread_counts' => $unreadNotifications
            ]);
    }
}
