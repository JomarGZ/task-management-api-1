<?php

namespace App\Http\Controllers\api\v1\notifications;

use App\Http\Controllers\api\v1\ApiController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationController extends ApiController
{
    public function index()
    {
        $user = auth()->user();
        $notifications = $user->notifications;
        return $this->success(
            'Notifications retrieved successfully',
            $notifications
        );
    }

    public function update($id)
    {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        return $this->success(
            'Notification mark read successfully',
            $notification
        );
    }
}
