<?php

namespace App\Clients;

use App\Models\EmailLog;
use Illuminate\Support\Facades\Log;
use SendGrid;
use SendGrid\Mail\Mail;
use Symfony\Component\HttpFoundation\Response;

class SendgridEmailClient implements EmailClient
{
    public const CLIENT_NAME = 'Sendgrid';

    private SendGrid $client;

    /**
     * @param  string  $apiKey
     */
    public function __construct(string $apiKey)
    {
        $this->client = new SendGrid($apiKey);
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return self::CLIENT_NAME;
    }

    /**
     * @return SendGrid
     */
    public function getClient(): SendGrid
    {
        return $this->client;
    }

    /**
     * @param  array  $email
     * @return bool
     */
    public function send(array $email): bool
    {
        try {
            $sendgridMail = new Mail(
                new SendGrid\Mail\From($email['from']['email'], $email['from']['name']),
                new SendGrid\Mail\To($email['to']['email'], $email['to']['name']),
                new SendGrid\Mail\Subject($email['subject']),
                null,
                new SendGrid\Mail\HtmlContent($email['htmlPart']),
            );

            $response = $this->client->send($sendgridMail);

            /** @var EmailLog $emailLog */
            $emailLog = EmailLog::where('email_id', $email['id'])->first();

            $emailLog->update([
                'email_provider' => self::CLIENT_NAME,
                'response' => json_encode($response->body()),
            ]);

            Log::debug(sprintf('Sendgrid response: %s %s', $response->statusCode(), $response->body()));

            return $response->statusCode() === Response::HTTP_ACCEPTED;
        } catch (\Exception $exception) {
            Log::error(sprintf('Sendgrid send error: %s', $exception->getMessage()));

            return false;
        }
    }
}
