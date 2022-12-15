<?php

namespace App\Clients;

use App\DTO\EmailDTO;
use App\Repositories\EmailLogRepositoryInterface;
use Illuminate\Support\Facades\Log;
use SendGrid;
use SendGrid\Mail\Mail;
use Symfony\Component\HttpFoundation\Response;

class SendgridEmailClient implements EmailClient
{
    public const CLIENT_NAME = 'Sendgrid';

    private EmailLogRepositoryInterface $emailLogRepository;
    private SendGrid $client;

    /**
     * @param  EmailLogRepositoryInterface  $emailLogRepository
     * @param  string  $apiKey
     */
    public function __construct(EmailLogRepositoryInterface $emailLogRepository, string $apiKey)
    {
        $this->emailLogRepository =$emailLogRepository;
        $this->setClient(new SendGrid($apiKey));
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return self::CLIENT_NAME;
    }

    /**
     * @param  SendGrid  $client
     * @return void
     */
    public function setClient(SendGrid $client): void
    {
        $this->client = $client;
    }

    /**
     * @return SendGrid
     */
    public function getClient(): SendGrid
    {
        return $this->client;
    }

    /**
     * @param  EmailDTO  $emailDTO
     * @return bool
     */
    public function send(EmailDTO $emailDTO): bool
    {
        try {
            $sendgridMail = new Mail(
                new SendGrid\Mail\From($emailDTO->from['email'], $emailDTO->from['name']),
                new SendGrid\Mail\To($emailDTO->to['email'], $emailDTO->to['name']),
                new SendGrid\Mail\Subject($emailDTO->subject),
                null,
                new SendGrid\Mail\HtmlContent($emailDTO->htmlPart),
            );

            $response = $this->client->send($sendgridMail);

            $this->emailLogRepository->update(
                $emailDTO->id,
                [
                    'email_provider' => self::CLIENT_NAME,
                    'response' => json_encode($response->body()),
                ]
            );

            Log::debug(sprintf('Sendgrid response: %s %s', $response->statusCode(), $response->body()));

            return $response->statusCode() === Response::HTTP_ACCEPTED;
        } catch (\Exception $exception) {
            Log::error(sprintf('Sendgrid send error: %s', $exception->getMessage()));

            return false;
        }
    }
}
