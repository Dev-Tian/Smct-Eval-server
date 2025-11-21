<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationsController extends Controller
{
    public function isRead(Request $request){
        // $user = Auth::user();
        $user = User::findOrFail(4);

        $notification = $user->notifications()->where('id', $request->id)->first();

        if (!$notification) {
            return response()->json([
                'message' => 'Notification not found'
            ], 404);
        }

        $notification->markAsRead();

        return response()->json([
            'message'         =>  'Mark as read notification',
        ],200);
    }
}
