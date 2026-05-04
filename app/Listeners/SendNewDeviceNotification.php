<?php

namespace App\Listeners;

use App\Events\NewDeviceLogin;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendNewDeviceNotification
{
    public function handle(NewDeviceLogin $event): void
    {
        $user = User::find($event->userId);
        
        if (!$user) {
            return;
        }

        $deviceInfo = $event->deviceInfo;
        
        Log::info('Enviando notificação de novo dispositivo', [
            'user_id' => $event->userId,
            'device' => $deviceInfo['platform'],
            'ip' => $deviceInfo['ip'],
        ]);

        // Envia email de notificação
        Mail::send('emails.new-device', [
            'user' => $user,
            'deviceInfo' => $deviceInfo,
            'time' => now()->format('d/m/Y H:i:s'),
        ], function ($message) use ($user) {
            $message->to($user->email)
                ->subject('Novo login detectado - Closer');
        });
    }
}
