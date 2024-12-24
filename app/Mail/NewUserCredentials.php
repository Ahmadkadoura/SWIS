<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\View;

class NewUserCredentials extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct($details)
    {
        $this->details = $details;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $htmlContent = "<h1>Welcome to Our Application!</h1>";
        $htmlContent .= "<p>Your account has been created. Here are your login details:</p>";
        $htmlContent .= "<p><strong>Email:</strong> {$this->details['email']}</p>";
        $htmlContent .= "<p><strong>Password:</strong> {$this->details['password']}</p>";
        $htmlContent .= "<p>Please change your password after logging in for the first time.</p>";
        $htmlContent .= "<p>Thank you for joining us!</p>";

        return $this->subject('Your New Account Credentials')
            ->html($htmlContent); // Directly set HTML content
    }



    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New User Credentials',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'view.name',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
