<?php

namespace App\Models;

use Ramsey\Uuid\Uuid;

class Email
{
    private string $id;
    private array $from;
    private array $to;
    private string $subject;
    private string $htmlPart;
    private EmailLog $emailLog;

    /**
     * @param  array  $from
     * @param  array  $to
     * @param  string  $subject
     * @param  string  $htmlPart
     */
    public function __construct(array $from, array $to, string $subject, string $htmlPart)
    {
        $this->id = Uuid::uuid4();
        $this->from = $from;
        $this->to = $to;
        $this->subject = $subject;
        $this->htmlPart = $htmlPart;

        $this->emailLog = (new EmailLog([
            'email_id' => $this->id,
            'status' => 'NEW',
        ]));
        $this->emailLog->save();
    }

    public function send(): array
    {
        $payload = [
            'id' => $this->id,
            'from' => $this->from,
            'to' => $this->to,
            'subject' => $this->subject,
            'htmlPart' => $this->htmlPart,
        ];

        \Amqp::publish('', json_encode($payload), ['queue' => 'email_queue',]);

        $this->emailLog->update([
            'status' => 'PROCESSING',
            'request' => json_encode($payload),
        ]);

        return $payload;
    }
}
