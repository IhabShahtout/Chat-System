<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Events\UserOffline;
use App\Events\UserOnline;
use App\Events\UserTyping;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ChatController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|object9
     */
    public function index()
    {
        $users = User::where('id', '!=', Auth::id())->get();
        return view('chat', compact('users'));
    }

    /**
     * @param $receiverId
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Http\JsonResponse|object
     */
    public function chat($receiverId, Request $request)
    {
        $receiver = User::findOrFail($receiverId);
        $messages = Message::where(function ($query) use ($receiverId) {
            $query->where('sender_id', Auth::id())->where('receiver_id', $receiverId);
        })->orWhere(function ($query) use ($receiverId) {
            $query->where('sender_id', $receiverId)->where('receiver_id', Auth::id());
        })->get();

        if ($request->ajax()) {
            return response()->json([
                'receiver' => [
                    'id' => $receiver->id,
                    'name' => $receiver->name,
                    'profile_picture_url' => $receiver->profile_picture_url,
                    'is_online' => $receiver->isOnline()
                ],
                'messages' => $messages
            ]);
        }

        return view('chat', compact('receiver', 'messages'));
    }

    /**
     * @param Request $request
     * @param $receiverId
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendMessage(Request $request, $receiverId)
    {
        try {
            // Validate the request
            $request->validate([
                'message' => 'required|string|max:1000',
            ]);

            // Check if the receiver exists
            $receiver = User::findOrFail($receiverId);

            // Create the message
            $message = Message::create([
                'sender_id' => Auth::id(),
                'receiver_id' => $receiverId,
                'message' => $request->message,
                'type' => 'text',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Broadcast the message
            broadcast(new MessageSent($message))->toOthers();

            // Return a success response
            return response()->json([
                'success' => true,
                'message' => 'Message sent successfully',
                'data' => $message
            ], 200);

        } catch (\Exception $e) {
            // Log the error
            \Log::error('Error sending message: ' . $e->getMessage());

            // Return an error response
            return response()->json([
                'success' => false,
                'message' => 'Failed to send message. Please try again.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function markOnline(Request $request)
    {
        $user = Auth::user();
        cache()->put('user-is-online-' . $user->id, true, now()->addMinutes(5));
        broadcast(new UserOnline($user))->toOthers();
        return response()->json(['status' => 'online']);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function markOffline(Request $request)
    {
        $user = Auth::user();
        cache()->forget('user-is-online-' . $user->id);
        broadcast(new UserOffline($user))->toOthers();
        return response()->json(['status' => 'offline']);
    }

    /**
     * @param Request $request
     * @param $receiverId
     * @return \Illuminate\Http\JsonResponse
     */
    public function typing(Request $request, $receiverId)
    {
        $sender = Auth::user();
        broadcast(new UserTyping($sender, $receiverId, $request->typing))->toOthers();
        return response()->json(['status' => 'typing broadcasted']);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeVoiceNote(Request $request)
    {
        try {
            $request->validate([
                'voice_note' => 'required|file|mimes:webm|max:10240', // 10MB max
                'receiver_id' => 'required|exists:users,id',
                'message' => 'nullable|string|max:1000', // Optional text message
            ]);

            $path = $request->file('voice_note')->store('voice_notes', 'public');
            $url = Storage::url($path);

            $message = Message::create([
                'sender_id' => auth()->id(),
                'receiver_id' => $request->receiver_id,
                'type' => 'voice',
                'voice_note' => $path,
                'message' => $request->input('message', ''), // Optional text, null if not provided
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            broadcast(new MessageSent($message))->toOthers();

            return response()->json(['success' => true, 'voice_note_url' => $url, 'message_id' => $message->id], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendAttachment(Request $request)
    {
        try {
            // Validate the request
            $request->validate([
                'receiver_id' => 'required|exists:users,id',
                'files' => 'required|array', // Expecting an array of files
                'files.*' => 'file|mimes:jpg,jpeg,png,gif|max:2048', // Validate each file
            ]);

            $attachments = [];
            foreach ($request->file('files') as $file) {
                // Store the file in the 'public' disk under 'chat_attachments'
                $path = $file->store('chat_attachments', 'public');
                $url = Storage::url($path);
                $originalName = $file->getClientOriginalName();
                $mimeType = $file->getClientMimeType();

                // Save to database (assuming a Message model)
                $message = new \App\Models\Message([
                    'sender_id' => auth()->id(),
                    'receiver_id' => $request->receiver_id,
                    'type' => 'attachment',
                    'attachment' => $path,
                    'message' => $request->input('message', ''), // Optional text could be added here if needed
                    'attachment_name' => $originalName,
                    'attachment_type' => $mimeType,
                ]);
                $message->save();

                $attachments[] = [
                    'url' => $url,
                    'type' => $mimeType,
                    'name' => $originalName,
                ];
            }

            broadcast(new MessageSent($message))->toOthers();

            return response()->json([
                'success' => true,
                'attachments' => $attachments,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(), // Return detailed error for debugging
            ], 500);
        }
    }
}
