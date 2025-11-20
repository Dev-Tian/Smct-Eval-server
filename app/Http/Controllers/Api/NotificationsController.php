<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationsController extends Controller
{
    public function myNotif(){
        $user           = Auth::user();
        $notifications  = $user->unreadNotifications;
        $count          = $user->unreadNotifications()->count();

        return response()->json([
            'notifications'         =>  $notifications,
            'count'                 =>  $count
        ],201);
    }
    public function isRead(Request $request){
        $user           = Auth::user();
        $user->notifications()->where('id', $request->id)->markAsRead();

        return response()->json([
            'message'         =>  'Mark as read notification',
        ],200);
    }
}
