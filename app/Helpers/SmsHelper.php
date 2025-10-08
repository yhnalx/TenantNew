<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;

class SmsHelper
{
    public static function send($to, $message)
    {
        $response = Http::post('https://philsms.com/api/v3/sms/send', [
            'api_key'   => env('PHILSMS_API_TOKEN'),
            'recipient' => $to,
            'sender_id' => env('PHILSMS_SENDER', 'PhilSMS'),
            'message'   => $message,
        ]);

        if ($response->successful()) {
            return $response->json();
        }

        return [
            'success' => false,
            'error' => $response->body(),
        ];
    }
}
