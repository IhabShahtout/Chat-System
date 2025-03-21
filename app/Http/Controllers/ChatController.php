<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Events\UserOnline;
use App\Events\UserTyping;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class ChatController extends Controller
{
    public function index()
    {
        $users = User::where('id', '!=', Auth::id())->get();
        return view('users', compact('users'));
    }

    public function chat($receiverId)
    {
        $receiver = User::find($receiverId);

        $messages = Message::where(function ($query) use ($receiverId){
            $query->where('sender_id', Auth::id())->where('receiver_id', $receiverId);
        })->orWhere(function ($query) use ($receiverId) {
            $query->where('sender_id', $receiverId)->where('receiver_id', Auth::id());
        })->get();

        // Get the last message if any
        $lastMessage = $messages->last();

        return view('chat', compact('receiver', 'messages', 'lastMessage'));
    }

    public function sendMessage(Request $request, $receiverId)
    {
        // save message to DB
        $message = Message::create([
            'sender_id'     => Auth::id(),
            'receiver_id'   => $receiverId,
            'message'       => $request['message']
        ]);

        // Fire the message event
        broadcast(new MessageSent($message))->toOthers();

        return response()->json(['status' => 'Message sent!']);
    }

    public function typing()
    {
        // Fire the typing event
        broadcast(new UserTyping(Auth::id()))->toOthers();
        return response()->json(['status' => 'typing broadcasted!']);
    }


    public function markOnline(Request $request)
    {
        $user = auth()->user();
        cache()->put('user-is-online-' . $user->id, true, now()->addMinutes(5));
        broadcast(new UserOnline($user))->toOthers();
        return response()->json(['status' => 'online']);
    }

    public function markOffline(Request $request)
    {
        $user = auth()->user();
        cache()->forget('user-is-online-' . $user->id);
        broadcast(new UserOffline($user))->toOthers();
        return response()->json(['status' => 'offline']);
    }

    public function setOnline()
    {
        Cache::put('user-is-online-' . Auth::id(), true, now()->addMinutes(5));
        return response()->json(['status' => 'Online']);
    }

    public function setOffline()
    {
        Cache::forget('user-is-online-' . Auth::id());
        return response()->json(['status' => 'Offline']);
    }

}
