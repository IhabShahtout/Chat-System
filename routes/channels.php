<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('user-status', function () {
    return true;
});

Broadcast::channel('chat.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});

Broadcast::channel('typing.{userId}', function ($user, $userId) {
    return true;
});
