<?php


namespace App\Http\Sender;

use Mailgun\Mailgun;

class EmailSender
{
    public function send(string $from, string $to, string $subject, string $text)
    {

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
