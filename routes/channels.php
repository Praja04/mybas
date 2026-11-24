<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Diskusi PK (Penilaian Karya) — kanal privat per NIK penerima.
Broadcast::channel('pk-diskusi.{nik}', function ($user, $nik) {
    $me = trim((string) ($user->nik ?? ''));
    if ($me === '') {
        $me = trim((string) ($user->username ?? ''));
    }

    return $me !== '' && strcasecmp($me, trim((string) $nik)) === 0;
});

// Diskusi IPP (Diskusi - KPI) untuk /hr/ipp-aktual & /hr/ipp-user-approval.
Broadcast::channel('ipp-diskusi.{nik}', function ($user, $nik) {
    $me = trim((string) ($user->nik ?? ''));
    if ($me === '') {
        $me = trim((string) ($user->username ?? ''));
    }

    return $me !== '' && strcasecmp($me, trim((string) $nik)) === 0;
});
