<?php

namespace App\Services;

use App\Http\Controllers\BaseController;
use App\Models\notification as notification1;
use App\Models\patient;
use App\Models\student;
use App\Models\User;
use App\Notifications\AdminNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class NotificationService extends BaseController
{
    public function sendMessage(Request $request)
    {
        $user = auth('sanctum')->user();
        notification1::create([
            'sender_id' => $user->id,
            'reciver_id' => $request->reciver_id,
            'title' => $request->title,
            'text' => $request->text,
        ]);
        $UserReciver=User::where('id',$request->reciver_id)->first();
        // dd($UserReciver->fcm_token);
        Notification::send(
            null,
            new AdminNotification(
                $request->title,
                $request->text,
                [$UserReciver->fcm_token]
            )
        );
        return "Done";
    }

    public function getMessages($id,$id2)
    {
        return notification1::where('sender_id',$id)->where('reciver_id',$id2)->get();
    }

    public function storeFcmToken(Request $request)
    {
        $user = auth('sanctum')->user();
        $user->fcm_token=$request->fcm_token;
        $user->update(['fcm_token' => $request->fcm_token]);
        return $user;
    }

    public function getNotifications()
    {
        $user = auth('sanctum')->user();
        $userSender=User::where('role','Admin')->first();

        $notifications=notification1::where('sender_id',244)->where('reciver_id',$user->id)->get();
        foreach($notifications as $notification){
            $notification->is_seen=true;
            $notification->save();
        }
        return $notifications;
    }

    public function isTheirNotifications()
    {
        $user = auth('sanctum')->user();
        return notification1::where('reciver_id',$user->id)->where('is_seen',false)->count();
    }

    // public function getPatietNotifications()
    // {
    //     $user = auth('sanctum')->user();
    //     $patient = patient::where('user_id', $user->id)->first();
    //     $userSender=User::where('type','Admin')->first();
    //     return notification1::where('sender_id',$userSender->id)->where('reciver_id',$patient->id)->get();
    // }
}
