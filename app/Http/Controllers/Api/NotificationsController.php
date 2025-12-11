<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationsController extends Controller
{
    public function isRead(Request $request)
    {
        $user = Auth::user();

        $notification = $user->notifications()->where('id', $request->id)->first();

        if (!$notification) {
            return response()->json([
                'message' => 'Notification not found'
            ], 404);
        }

        $notification->markAsRead();

        return response()->json([
            'message'         =>  'Mark as read notification',
        ], 200);
    }

    public function markAllAsRead()
    {

        $user = Auth::user();

        $user->unreadNotifications->markAllAsRead();

        return response()->json([
            'message'       =>  "Successfully read all notifications"
        ], 200);
    }
}
