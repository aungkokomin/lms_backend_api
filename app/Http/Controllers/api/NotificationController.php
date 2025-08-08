<?php

namespace App\Http\Controllers\api;

use App\Events\MyEvent;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserNotificationResource;
use App\Models\User;
use App\Notifications\RealTimeNotification;
use Illuminate\Http\Request;
use Illuminate\Log\Logger;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Pusher\Pusher;

class NotificationController extends Controller
{
    //
    protected $notification;

    public function __construct(DatabaseNotification $notification)
    {
        $this->notification = $notification;
    }

    public function index(Request $request)
    {
        try{
            $user = Auth::user();
            $notifications = $user->notifications()->orderByDesc('created_at')->limit(10)->get();
            return response()->json(
            UserNotificationResource::collection($notifications)
            , 200);
        }catch(\Exception $e){
            return response()->json(['message' => $e->getMessage()],400);
        }
    }

    public function unreadCounts()
    {
        $user = Auth::user();
        $unreadCount = $user->unreadNotifications->count();
        return response()->json(['unread_count' => $unreadCount]);
    }

    public function markAsRead($id)
    {
        $notification = $this->notification->findOrFail($id);
        $notification->markAsRead();
        return response()->json(['message' => 'Notification marked as read']);
    }

    public function destroy($id)
    {
        $notification = $this->notification->findOrFail($id);
        $notification->delete();
        return response()->json(['message' => 'Notification deleted']);
    }

    public function markAllAsRead(Request $request)
    {
        $user = $request->user();
        $user->unreadNotifications->markAsRead();
        return response()->json(['message' => 'All notifications marked as read']);
    }

    public function destroyAll(Request $request)
    {
        $user = $request->user();
        $user->notifications()->delete();
        return response()->json(['message' => 'All notifications deleted']);
    }

    public function sendNotification(Request $request)
    {
        try{
            $user = User::find(Auth::id());
            $message = $request->input('message');
            // Notification::send($user, new RealTimeNotification($message));
            $user->notify(new RealTimeNotification($message,$user->id));
            
            return response()->json(['message' => 'Notification sent']);
            // event(new MyEvent($request->input("message")));
            // return response()->json([
            //     'message' => 'Notification sent',
            // ]);
        }catch(\Exception $e){
            return response()->json(['message' => $e->getMessage()],400);
        }
    }

    public function testNotification(Request $request)
    {
        try{
            $user = User::find(Auth::id());
            $message = $request->input('message');
            $user->notify(new RealTimeNotification($message,$user->id));
            return response()->json(['message' => 'Notification sent']);
        }catch(\Exception $e){
            return response()->json(['message' => $e->getMessage()],400);
        }
    }

    public function pusherAuth(Request $request)
    {
        $user = Auth::user();
        $socket_id = $request->input('socket_id');
        $channel_name = $request->input('channel_name');
        $pusher = new Pusher(config('services.pusher.APP_KEY'), config('services.pusher.APP_SECRET'), config('services.pusher.APP_ID'), [
            'cluster' => 'ap2',
            'useTLS' => true
        ]);
        $presence_data = ['name' => $user->name,'id' => $user->id];
        $key = json_decode($pusher->authenticateUser($socket_id, $presence_data));
        return response()->json($key);
    }
}
