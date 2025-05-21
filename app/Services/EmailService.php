<?php

namespace App\Services;

use Illuminate\Support\Facades\Mail;

class EmailService
{
    public function sendEmail(string $subject, string $content)
    {
        Mail::raw($content, function ($message) use ($subject) {
            $message->from(config('mail.from.address'), config('mail.from.name'))
                ->to(config('mail.from.address')) // Same as from for simplicity
                ->subject($subject);
        });
    }
}
