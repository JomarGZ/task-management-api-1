<?php

namespace App\Http\Controllers\api\v1\notifications;

use App\Http\Controllers\api\v1\ApiController;
use App\Http\Controllers\Controller;
use App\Http\Resources\api\v1\Notifications\NotificationResource;
use Illuminate\Http\Request;
use Illuminate\Notifications\Notification;

class NotificationController extends ApiController
{
    public function index(Request $request)
    {
        $filterType = $request->query('type');
      
        $user = auth()->user();
        $notifications = $user
            ->notifications()
            ->when($filterType, function ($query) use ($filterType) {
                return match ($filterType) {
                    'unread' => $query->whereNull('read_at'),
                    default => $query
                };
            })
            ->latest()
            ->paginate(10);
        return NotificationResource::collection($notifications)->additional(['message' => 'Notification retrieved successfully']);
    }

    public function update($id)
    {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        return NotificationResource::make($notification->fresh())->additional(['message' => 'Notification updated successfully']);
    }

    public function destroy($id)
    {
        $user = request()->user();
        $notification = $user->notifications()->findOrFail($id);
        $notification->delete();
        return response()->noContent();
    }
}
