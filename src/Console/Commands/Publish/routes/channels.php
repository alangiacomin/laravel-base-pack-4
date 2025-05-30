<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('channel-name', function ($user, $id) {
    return true;
});
