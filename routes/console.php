<?php

use App\Mail\WelcomeMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

// Example default command
Artisan::command('inspire', function () {
    $this->comment(\Illuminate\Foundation\Inspiring::quote());
})->purpose('Display an inspiring quote');

// ✅ Run update:tenant-status automatically every midnight
Schedule::command('update:tenant-status')
    // ->dailyAt('00:00')
    ->everyMinute()
    ->appendOutputTo(storage_path('logs/tenant_status.log'));

Artisan::command('send-welcome-mail', function () {
    $recipientEmail = 'jalix2003@gmail.com';
    $recipientName  = 'Yuehan';

    Mail::to($recipientEmail)->send(new WelcomeMail($recipientName, $recipientEmail));

    // If you want to use a specific mailer (like Mailtrap SDK) instead of default
    // Mail::mailer('mailtrap-sdk')->to($recipientEmail)->send(new WelcomeMail($recipientName, $recipientEmail));

    $this->info("✅ Welcome email sent to {$recipientEmail}");
})->purpose('Send welcome mail');
