<?php


namespace App\Http\Sender;

use Mailgun\Mailgun;

class EmailSender
{
    public function send(string $from, string $to, string $subject, $token)
    {
        $text = 'Будь ласка,перейдіть за посиланням :' . url("register/confirm/{$token}");
        $domain = env('MAILGUN_DOMAIN');
        $key = env('MAILGUN_SECRET');

        $email = [
            'from' => $from,
            'to' => $to,
            'subject' => $subject,
            'text' => $text
        ];

        Mailgun::create($key)
            ->messages()
            ->send($domain, $email);
    }

}
