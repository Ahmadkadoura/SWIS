<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PasswordResetCodeMail extends Mailable
{
    use Queueable, SerializesModels;

    public $resetCode;

    public function __construct($resetCode)
    {
        $this->resetCode = $resetCode;
    }

    public function build()
    {
        $htmlContent = "<h1>Password Reset Request</h1>";
        $htmlContent .= "<p>You requested to reset your password. Please use the following code to reset your password:</p>";
        $htmlContent .= "<p><strong>Reset Code:</strong> {$this->resetCode}</p>";
        $htmlContent .= "<p>This code is valid for 15 minutes.</p>";
        $htmlContent .= "<p>If you did not request a password reset, please ignore this email.</p>";
        $htmlContent .= "<p>Thank you for using our application!</p>";

        return $this->subject('Your Password Reset Code')
            ->html($htmlContent); // Send raw HTML content
    }
}
