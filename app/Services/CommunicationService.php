<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class CommunicationService
{
    public function sendSms(string $to, string $message): bool
    {
        $endpoint = config('services.sms.endpoint');
        $token = config('services.sms.token');

        if (! $endpoint || ! $token) {
            return false;
        }

        $response = Http::withToken($token)->post($endpoint, [
            'to' => $to,
            'message' => $message,
        ]);

        return $response->successful();
    }

    public function sendWhatsapp(string $to, string $message): bool
    {
        $endpoint = config('services.whatsapp.endpoint');
        $token = config('services.whatsapp.token');

        if (! $endpoint || ! $token) {
            return false;
        }

        $response = Http::withToken($token)->post($endpoint, [
            'to' => $to,
            'message' => $message,
        ]);

        return $response->successful();
    }
}
