<?php

use Illuminate\Support\Facades\Broadcast;

// User-specific private channel for notifications and status alerts
Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->user_id === (int) $id;
});

// Broadcast channel for public job vacancy announcements
Broadcast::channel('job-vacancies', function () {
    return true; // Public or authenticated
});
