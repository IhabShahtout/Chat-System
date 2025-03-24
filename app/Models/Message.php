<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{

    protected $fillable = [
        'sender_id',
        'receiver_id',
        'message',
        'voice_note',
        'type',
        'attachment',
        'attachment_type'
    ];

    protected $casts = [
        'is_voice' => 'boolean',
    ];

    protected $appends = ['is_voice'];

    public function rules()
    {
        return [
            'sender_id' => 'required|exists:users,id',
            'receiver_id' => 'required|exists:users,id',
            'message' => 'nullable|string',
            'voice_note' => 'nullable|string',
            'type' => 'required|in:text,voice'
        ];
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function getIsVoiceAttribute()
    {
        // Logic to determine if the message is a voice note
        return $this->type === 'voice';
    }
}
