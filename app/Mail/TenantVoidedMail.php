<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Headers;
use Illuminate\Queue\SerializesModels;
use Mailtrap\EmailHeader\CategoryHeader;
use Mailtrap\EmailHeader\CustomVariableHeader;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Header\UnstructuredHeader;
use App\Models\User;

class TenantVoidedMail extends Mailable
{
    use Queueable, SerializesModels;

    public User $tenant;

    /**
     * Create a new message instance.
     */
    public function __construct(User $tenant)
    {
        $this->tenant = $tenant;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address('no-reply@yourdomain.com', 'Property Management System'),
            subject: 'Account Voided - Deposit Not Paid',
            using: [
                function (Email $email) {
                    // Headers
                    $email->getHeaders()
                        ->addTextHeader('X-Message-Source', 'yourdomain.com')
                        ->add(new UnstructuredHeader('X-Mailer', 'Mailtrap PHP Client'));

                    // Optional Custom Variables
                    $email->getHeaders()
                        ->add(new CustomVariableHeader('tenant_id', (string) $this->tenant->id));

                    // Category (for Mailtrap or tracking)
                    $email->getHeaders()
                        ->add(new CategoryHeader('Tenant Account Notification'));
                },
            ]
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.tenant_voided',
            with: [
                'name' => $this->tenant->name,
                'email' => $this->tenant->email,
            ],
        );
    }

    /**
     * Get the message headers.
     */
    public function headers(): Headers
    {
        return new Headers(
            'custom-message-id@yourdomain.com',
            [],
            [
                'X-Custom-Header' => 'Tenant Voided Notification',
            ]
        );
    }
}
