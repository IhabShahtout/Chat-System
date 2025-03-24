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
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use FFMpeg\FFMpeg;
use FFMpeg\FFProbe;
use FFMpeg\Format\Audio\Wav;
use Illuminate\Support\Facades\Storage;

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

        $messages = Message::where(function ($query) use ($receiverId) {
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
            'sender_id' => Auth::id(),
            'receiver_id' => $receiverId,
            'message' => $request['message']
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


    public function sendVoiceNote(Request $request)
    {
        try {
            $validated = $request->validate([
                'voice_note' => 'required|file|mimes:mp3,wav,ogg,webm,audio/webm,audio/mpeg,audio/wav,video/webm|max:10240',
                'receiver_id' => 'required|exists:users,id'
            ]);

            $user = auth()->user();
            $receiver = User::findOrFail($request->receiver_id);
            $voiceNote = $request->file('voice_note');
            $realPath = $voiceNote->getRealPath();

            Log::info('Voice note uploaded:', [
                'mime' => $voiceNote->getMimeType(),
                'size' => $voiceNote->getSize(),
                'path' => $realPath
            ]);

            if (!file_exists($realPath) || $voiceNote->getSize() == 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error processing voice note: Uploaded file is invalid or empty'
                ], 422);
            }

            $ffmpegPath = 'C:\ffmpeg\bin\ffmpeg.exe';
            $ffprobePath = 'C:\ffmpeg\bin\ffprobe.exe';

            Log::info('FFmpeg path:', ['path' => $ffmpegPath, 'exists' => file_exists($ffmpegPath)]);
            Log::info('FFprobe path:', ['path' => $ffprobePath, 'exists' => file_exists($ffprobePath)]);

            $ffmpeg = FFMpeg::create([
                'ffmpeg.binaries'  => $ffmpegPath,
                'ffprobe.binaries' => $ffprobePath,
                'ffmpeg.threads'   => 12,
            ]);

            $ffprobe = $ffmpeg->getFFProbe();
            $streams = $ffprobe->streams($realPath);
            $audioStreams = $streams->audios();

            $streamDetails = array_map(function ($stream) {
                return [
                    'codec_type' => $stream->get('codec_type'),
                    'codec_name' => $stream->get('codec_name'),
                    'duration' => $stream->get('duration'),
                ];
            }, $streams->all());
            $audioStreamDetails = array_map(function ($stream) {
                return [
                    'codec_type' => $stream->get('codec_type'),
                    'codec_name' => $stream->get('codec_name'),
                    'duration' => $stream->get('duration'),
                ];
            }, $audioStreams->all());

            Log::info('FFprobe streams detailed:', ['streams' => $streamDetails]);
            Log::info('FFprobe audio streams detailed:', ['audio_streams' => $audioStreamDetails]);
            Log::info('FFprobe format:', ['format' => $ffprobe->format($realPath)->all()]);

            if (empty($audioStreamDetails)) {
                throw new \Exception('No audio stream found in the uploaded file');
            }

            $audio = $ffmpeg->open($realPath);

            $format = new Wav();
            $format->setAudioCodec('pcm_s16le');
            $format->setAudioChannels(1);
            $format->setAudioKiloBitrate(128);

            $filename = uniqid() . '.wav';
            $directory = storage_path('app/public/voice-notes');
            $storagePath = "{$directory}/{$filename}";

            if (!file_exists($directory)) {
                if (!mkdir($directory, 0777, true) && !is_dir($directory)) {
                    throw new \Exception("Failed to create directory: {$directory}");
                }
                Log::info('Directory created:', ['directory' => $directory]);
            }

            if (!is_writable($directory)) {
                throw new \Exception("Directory is not writable: {$directory}");
            }

            Log::info('Saving voice note:', [
                'storage_path' => $storagePath,
                'directory_exists' => file_exists($directory),
                'is_writable' => is_writable($directory)
            ]);

            $audio->save($format, $storagePath);

            $message = Message::create([
                'sender_id' => $user->id,
                'receiver_id' => $receiver->id,
                'message' => '', // Add default empty string for text messages
                'voice_note' => "voice-notes/{$filename}",
                'type' => 'voice'
            ]);

            broadcast(new MessageSent($message, $user))->toOthers();

            return response()->json([
                'success' => true,
                'voice_note_url' => asset("storage/voice-notes/{$filename}")
            ]);
        } catch (\Exception $e) {
            Log::error('Voice note processing failed: ', [
                'error' => $e->getMessage(),
                'file' => $request->file('voice_note') ? $voiceNote->getClientOriginalName() : 'N/A',
                'mime' => $request->file('voice_note') ? $voiceNote->getMimeType() : 'N/A',
                'size' => $request->file('voice_note') ? $voiceNote->getSize() : 0,
                'previous' => $e->getPrevious() ? $e->getPrevious()->getMessage() : 'N/A'
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Error processing voice note: ' . $e->getMessage()
            ], 500);
        }
    }

    public function uploadAttachment(Request $request)
    {
        try {
            $request->validate([
                'file' => 'required|max:25000|mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx,ppt,pptx,txt',
                'receiver_id' => 'required|exists:users,id'
            ]);

            $file = $request->file('file');
            $path = $file->store('attachments', 'public');

            $message = Message::create([
                'sender_id' => auth()->id(),
                'receiver_id' => $request->receiver_id,
                'type' => 'attachment',
                'message' => '',
                'attachment' => $path,
                'attachment_type' => $file->getClientMimeType(),
                'attachment_name' => $file->getClientOriginalName()
            ]);
            broadcast(new MessageSent($message))->toOthers();
            return response()->json([
                'success' => true,
                'attachment' => [
                    'name' => $file->getClientOriginalName(),
                    'type' => $file->getClientMimeType(),
                    'url' => Storage::disk('public')->url($path)
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

}
