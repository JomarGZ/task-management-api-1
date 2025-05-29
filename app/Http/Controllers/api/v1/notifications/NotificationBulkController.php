<?php

namespace App\Http\Controllers\api\v1\notifications;

use App\Http\Controllers\Controller;
use App\Http\Resources\api\v1\Notifications\NotificationResource;
use Illuminate\Http\Request;

class NotificationBulkController extends Controller
{
    public function update()
    {
        $user = request()->user();
        $unreadNotifications = $user->unreadNotifications()->get();
        $user->unreadNotifications()->update(['read_at' => now()]);

        return NotificationResource::collection($unreadNotifications)->additional(['message' => 'Notifications mark read all successfully']);
    }

    public function destroy()
    {
        $user = request()->user();
        $user->notifications()->delete();

        return response()->noContent();
    }
}
