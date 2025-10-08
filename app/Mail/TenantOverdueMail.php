<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class TenantOverdueMail extends Mailable
{
    use Queueable, SerializesModels;

    public $tenant;

    public function __construct(User $tenant)
    {
        $this->tenant = $tenant;
    }

    public function build()
    {
        return $this->subject('Payment Overdue Notice')
            ->view('emails.tenant_overdue');
    }
}
